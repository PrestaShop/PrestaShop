<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\Mail;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This is a basic mail templates catalog, not a lot of intelligence it is based
 * simply on existing files on the $mailThemesFolder (no database, or config files).
 */
class MailTemplateFolderCatalog implements MailTemplateCatalogInterface
{
    /**
     * @var string
     */
    private $mailThemesFolder;

    /**
     * MailTemplateFolderCatalog constructor.
     *
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
     * {@inheritdoc}
     */
    public function listTemplates($theme)
    {
        $finder = new Finder();
        $templates = [];

        //Core templates
        $coreTemplatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            MailTemplateInterface::CORE_TEMPLATES,
        ]);
        $finder->files()->in($coreTemplatesFolder)->sortByName();
        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $suffix = !empty($fileInfo->getExtension()) ? '.' . $fileInfo->getExtension() : '';
            $templates[] = new MailTemplate(
                $theme,
                MailTemplateInterface::CORE_TEMPLATES,
                $fileInfo->getBasename($suffix),
                $fileInfo->getRealPath()
            );
        }

        //Modules templates
        $moduleTemplatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            MailTemplateInterface::MODULES_TEMPLATES,
        ]);
        $moduleFinder = new Finder();
        $moduleFinder->directories()->in($moduleTemplatesFolder)->depth(0);

        /* @var SplFileInfo $fileInfo */
        foreach ($moduleFinder as $moduleFileInfo) {
            $moduleName = $moduleFileInfo->getFilename();
            $finder = new Finder();
            $finder->files()->in($moduleTemplatesFolder . DIRECTORY_SEPARATOR . $moduleName)->depth(0)->sortByName();
            /** @var SplFileInfo $fileInfo */
            foreach ($finder as $fileInfo) {
                $suffix = !empty($fileInfo->getExtension()) ? '.' . $fileInfo->getExtension() : '';
                $templates[] = new MailTemplate(
                    $theme,
                    MailTemplateInterface::MODULES_TEMPLATES,
                    $fileInfo->getBasename($suffix),
                    $fileInfo->getRealPath(),
                    $moduleName
                );
            }
        }

        return new MailTemplateCollection($templates);
    }
}
