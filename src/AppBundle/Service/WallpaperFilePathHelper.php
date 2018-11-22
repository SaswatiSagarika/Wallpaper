<?php

namespace AppBundle\Service;

class WallpaperFilePathHelper
{
    
    public function getNewFilePath(string $newFileName)
    {
        global $kernel;
        $wallpaperFath = $kernel->getContainer()->getParameter('data_dir');
        return  $wallpaperFath.$newFileName;
    }
}