<?php

namespace PrestaShopBundle\Utils;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ZipManager
{
    public function createArchive($filename, $folder)
    {
        $zip = new \ZipArchive();

        $zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $filename => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filename, strlen($folder) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }
}
