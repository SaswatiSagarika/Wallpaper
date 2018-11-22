<?php
/**
 * Controller for Default apitesting functions.
 *
 * @author Saswati
 *
 * @category Controller
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormTypeInterface;
use AppBundle\Form\UploadFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Entity\UploadLog;
use Symfony\Component\Form\Form;


class UploadController extends Controller
{   
    /**
     * function to call the api from symfony form
     *
     *@Route("/form", name="form")
     * @param $request
     * @return array
     */
    public function indexAction (Request $request)
    {	
        $student = new UploadLog();
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //path for the upload
            $upload_directory = $this->getParameter('upload_directory');
            $file = $form["csvfile"]->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($upload_directory, $fileName);
          
            //parse the file
            $files = $this->container
                ->get('app.service.split_csv')
                ->splitCsv($fileName )
            ;
            //zip the files
            $zip = new \ZipArchive();
            $zipName = $upload_directory.'Documents.zip';

            $zip->open($zipName,  \ZipArchive::CREATE);
            foreach ($files as $f) {
                $zip->addFile($f);
            }
            $zip->close();
            
            //response to download the zip file
            $response = new Response(file_get_contents($zipName));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
            $response->headers->set('Content-length', filesize($zipName));

            return $response;
        }
        return $this->render('default/upload.html.twig', ['form' => $form->createView()]);
        
    }
}