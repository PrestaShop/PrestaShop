<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This is a basic mail templates catalog, not a lot of intelligence it is based
 * simply on existing files on the $mailThemesFolder (no database, or config files).
 */
class MailTemplateFolderCatalog implements MailTemplateCatalogInterface
{
    /** @var string */
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
     * @throws InvalidException
     *
     * @return string[]
     */
    public function listThemes()
    {
        $this->checkTemplatesFolder();

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
        $this->checkTemplatesFolder();

        $collection = new MailTemplateCollection();
        $this->listCoreTemplates($collection, $theme);
        $this->listModulesTemplates($collection, $theme);

        return $collection;
    }

    /**
     * @param MailTemplateCollectionInterface $collection
     * @param string $theme
     */
    private function listCoreTemplates(MailTemplateCollectionInterface $collection, $theme)
    {
        $coreTemplatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            MailTemplateInterface::CORE_CATEGORY,
        ]);
        if (!file_exists($coreTemplatesFolder) || !is_dir($coreTemplatesFolder)) {
            return;
        }

        $this->addTemplatesFromFolder($collection, $coreTemplatesFolder, $theme, MailTemplateInterface::CORE_CATEGORY);
    }

    /**
     * @param MailTemplateCollectionInterface $collection
     * @param string $theme
     */
    private function listModulesTemplates(MailTemplateCollectionInterface $collection, $theme)
    {
        $moduleTemplatesFolder = implode(DIRECTORY_SEPARATOR, [
            $this->mailThemesFolder,
            $theme,
            MailTemplateInterface::MODULES_CATEGORY,
        ]);
        if (!file_exists($moduleTemplatesFolder) || !is_dir($moduleTemplatesFolder)) {
            return;
        }

        $moduleFinder = new Finder();
        $moduleFinder->directories()->in($moduleTemplatesFolder)->depth(0);

        /* @var SplFileInfo $fileInfo */
        foreach ($moduleFinder as $moduleFileInfo) {
            $moduleName = $moduleFileInfo->getFilename();
            $moduleFolder = implode(DIRECTORY_SEPARATOR, [$moduleTemplatesFolder, $moduleName]);
            $this->addTemplatesFromFolder($collection, $moduleFolder, $theme, MailTemplateInterface::MODULES_CATEGORY, $moduleName);
        }
    }

    /**
     * @param MailTemplateCollectionInterface $collection
     * @param string $folder
     * @param string $theme
     * @param string $category
     * @param string|null $moduleName
     */
    private function addTemplatesFromFolder(
        MailTemplateCollectionInterface $collection,
        $folder,
        $theme,
        $category,
        $moduleName = null
    ) {
        $templateTypes = [
            MailTemplateInterface::HTML_TYPE,
            MailTemplateInterface::RAW_TYPE,
        ];

        foreach ($templateTypes as $templateType) {
            $typeFolder = implode(DIRECTORY_SEPARATOR, [$folder, $templateType]);
            if (!file_exists($typeFolder) || !is_dir($typeFolder)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($typeFolder)->depth(0)->sortByName();
            /** @var SplFileInfo $fileInfo */
            foreach ($finder as $fileInfo) {
                $suffix = !empty($fileInfo->getExtension()) ? '.' . $fileInfo->getExtension() : '';
                $collection->add(new MailTemplate(
                    $theme,
                    $category,
                    $templateType,
                    $fileInfo->getBasename($suffix),
                    $fileInfo->getRealPath(),
                    $moduleName
                ));
            }
        }
    }

    /**
     * @throws InvalidException
     */
    private function checkTemplatesFolder()
    {
        if (!file_exists($this->mailThemesFolder) || !is_dir($this->mailThemesFolder)) {
            throw new InvalidException(sprintf(
                'Invalid mail themes folder "%s": no such directory',
                $this->mailThemesFolder
            ));
        }
    }
}
