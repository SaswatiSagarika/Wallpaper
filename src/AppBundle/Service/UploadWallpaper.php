<?php

/**
 * Description of ImportCSVService
 *
 *
 */
namespace AppBundle\Service;

use AppBundle\Entity\Wallpaper;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class UploadWallpaper
{

    /**
     * @var EntityManagerInterface
     */
    protected $doctrine;

    /**
     * @param $translator
     * @param $service_container
     *
     * @return void
     */

    public function __construct(EntityManagerInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Function to import Users
     *
     * @param $sheet    obejct(PHPExcel_Worksheet)
     *
     * @return array
     *
     **/
    public function uploadImages()
    {   
        try {

            $em = $this->doctrine->getManager();
        
            // Path to CSV file
            global $kernel;
            $path = $kernel->getContainer()->getParameter('data_dir');
            $wallPapers= glob($path."/images/*.jpg");

            
            foreach ($wallPapers as $wallPaper) {

                $wp = new Wallpaper;
                $wp->setFilename($wallPaper)
                   ->setSlug(strtok(end(explode('/',$wallPaper)), '_'))
                   ->setWidth(1200)
                   ->setHeight(675);
                $em->persist($wp);
            }
            
            $em->flush();
        } catch (\Exception $e) {
            
            $returnData['errorMessage'] = $e->getMessage();
        }

        return $returnData;
    }
}
