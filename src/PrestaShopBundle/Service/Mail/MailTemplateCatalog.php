<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 2018-12-21
 * Time: 17:19
 */

namespace PrestaShopBundle\Service\Mail;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MailTemplateCatalog
{
    /**
     * @var string
     */
    private $mailThemesFolder;

    public function __construct($mailThemesFolder)
    {
        $this->mailThemesFolder = $mailThemesFolder;
    }

    /**
     * @return string[]
     */
    public function listThemes()
    {
        $finder = new Finder();
        $finder->files()->in($this->mailThemesFolder);

        $themes = [];
        /** @var SplFileInfo $themeFolder */
        foreach ($finder as $themeFolder) {
            $themes[] = $themeFolder->getFilename();
        }

        return $themes;
    }

    /**
     * @param string $theme
     * @return string[]
     */
    public function listTemplates($theme)
    {
        $themeFolder = $this->mailThemesFolder . DIRECTORY_SEPARATOR . $theme;
        $finder = new Finder();

        $templates = [];
        foreach (['core', 'modules'] as $templatesType) {
            $templatesFolder = $themeFolder . DIRECTORY_SEPARATOR . $templatesType;
            $finder->files()->in($templatesFolder);
            /** @var SplFileInfo $fileInfo */
            foreach ($finder as $fileInfo) {
                $templates[] = $fileInfo->getFilename();
            }
        }

        return $templates;
    }
}
