<?php

/**
 * Description of SplitCSVService
 *
 *
 */
namespace AppBundle\Service;

use League\Csv\Reader;
use AppBundle\Entity\UploadLog;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use \PhpOffice\PhpSpreadsheet\Writer\IWriter;
use \PhpOffice\PhpSpreadsheet\Reader\IReader;

class SplitCSVService
{

    /**
     * Function to split the csv
     *
     * @param $sheet    obejct(PHPExcel_Worksheet)
     *
     * @return array
     *
     **/
    public function splitCsv($sheet)
    {   
        try {
            
            // Path to CSV file
            global $kernel;
            $path = $kernel->getContainer()->getParameter('upload_directory');
            $filePath= $path.$sheet;

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            // $inputFileType = IOFactory::identify($filePath);
            // $reader = IOFactory::load($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
             
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $count = count($sheetData);
            $firstrow = $sheetData[0];
            $linecount = 0;
            $chunkSize = 1000;
            $i = 0;
            while ($linecount < $count) 
            { 
                
                $newSpreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path.'c.xlsx');

                $sheet = $newSpreadsheet->setActiveSheetIndex(0);
                $sheet->setCellValue('A1', $firstrow[0])
                         ->setCellValue('B1', $firstrow[1])
                         ->setCellValue('C1', $firstrow[2])
                         ->setCellValue('D1', $firstrow[3])
                         ->setCellValue('E1', $firstrow[4])
                         ->setCellValue('F1', $firstrow[5])
                         ->setCellValue('G1', $firstrow[6])
                         ->setCellValue('H1', $firstrow[7]);
                
                $spliceSheetDatas = array_slice($sheetData, $linecount+1, $chunkSize);
                $idx = 2;
                foreach ($spliceSheetDatas as $value) {
                    // fill the file to the  lines
                    
                    $sheet->setCellValue('A'.$idx, $value[0])
                         ->setCellValue('B'.$idx, $value[1])
                         ->setCellValue('C'.$idx, $value[2])
                         ->setCellValue('D'.$idx, $value[3])
                         ->setCellValue('E'.$idx, $value[4])
                         ->setCellValue('F'.$idx, $value[5])
                         ->setCellValue('G'.$idx, $value[6])
                         ->setCellValue('H'.$idx, $value[7]);
                    $idx++;
                    $linecount++;
                     
                }
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sheet, 'xlsx');
                print_r($writer);exit();
                $writer->save($path.$linecount.'.xlsx');
                $returnData[$i] = $path.$linecount.'.xlsx';
                $i++;
            }
        } catch (\Exception $e) {
           

            $returnData['errorMessage'] = $e->getMessage();
            print_r($returnData);exit();
        }

        return $returnData;
    }
}

