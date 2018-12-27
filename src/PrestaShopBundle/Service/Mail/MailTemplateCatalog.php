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
    const CORE_TEMPLATES = 'core';
    const MODULES_TEMPLATES = 'modules';

    /**
     * @var string
     */
    private $mailThemesFolder;

    /**
     * MailTemplateCatalog constructor.
     * @param string $mailThemesFolder
     */
    public function __construct($mailThemesFolder)
    {
        $this->mailThemesFolder = $mailThemesFolder;
    }

    /**
     * Returns the list of found themes (non empty folders, in the mail themes
     * folder).
     *
     * @return string[]
     */
    public function listThemes()
    {
        $finder = new Finder();
        $finder->directories()->in($this->mailThemesFolder)->depth(0);

        $themes = [];
        /** @var SplFileInfo $themeFolder */
        foreach ($finder as $themeFolder) {
            $dirFinder = new Finder();
            $dirFinder->files()->in($themeFolder->getRealPath());
            if ($dirFinder->count() > 0) {
                $themes[] = $themeFolder->getFilename();
            }
        }

        return $themes;
    }

    /**
     * Returns the list of core templates for the requested theme.
     *
     * @param string $theme
     * @return string[]
     */
    public function listCoreTemplates($theme)
    {
        $templatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            self::CORE_TEMPLATES,
        ]);

        return $this->listTemplates($templatesFolder);
    }

    /**
     * Returns the list of templates by module for the requested theme.
     *
     * @param string $theme
     * @return array
     */
    public function listModuleTemplates($theme)
    {
        $templates = [];
        $moduleTemplatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            self::MODULES_TEMPLATES,
        ]);
        $finder = new Finder();
        $finder->directories()->in($moduleTemplatesFolder);

        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $moduleName = $fileInfo->getFilename();
            $moduleTemplates = $this->listTemplates($moduleTemplatesFolder . DIRECTORY_SEPARATOR . $moduleName);
            if (count($moduleTemplates) > 0) {
                $templates[$moduleName] = $moduleTemplates;
            }
        }

        return $templates;
    }

    /**
     * @param string $templatesFolder
     * @return array
     */
    private function listTemplates($templatesFolder)
    {
        $templates = [];
        $finder = new Finder();
        $finder->files()->in($templatesFolder)->sortByName();
        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $suffix = !empty($fileInfo->getExtension()) ? '.' . $fileInfo->getExtension() : '';
                $templates[] = $fileInfo->getBasename($suffix);
        }

        return $templates;
    }
}
