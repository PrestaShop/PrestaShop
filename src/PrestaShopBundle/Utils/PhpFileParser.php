<?php

namespace PrestaShopBundle\Utils;

use Symfony\Component\Finder\SplFileInfo;

class PhpFileParser
{
    public static function getCLassName(SplFileInfo $file)
    {
        $phpContent = file_get_contents($file->getRealPath());
        if (preg_match('~namespace[ \t]+(.+)[ \t]*;~Um', $phpContent, $matches)) {
            $className = sprintf('%s\%s', $matches[1], $file->getBasename('.php'));
            if (class_exists($className)) {
                return $className;
            }
        }

        return null;
    }
}
