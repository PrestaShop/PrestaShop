<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FolderThemeScanner is used to scan a mail theme folder, it returns a ThemeInterface with all
 * its layouts.
 */
final class FolderThemeScanner
{
    /**
     * @var string
     */
    private $baseThemeFolder;

    /**
     * @param string $mailThemeFolder
     *
     * @return ThemeInterface|null
     *
     * @throws FileNotFoundException
     * @throws TypeException
     */
    public function scan($mailThemeFolder)
    {
        $this->checkThemeFolder($mailThemeFolder);

        $mailTheme = new Theme(basename($mailThemeFolder));
        $this->baseThemeFolder = dirname($mailThemeFolder);

        $finder = new Finder();
        $finder->files()->in($mailThemeFolder);
        if ($finder->count() > 0) {
            $mailTheme->setLayouts($this->findThemeLayouts($mailThemeFolder));
        }

        return $mailTheme;
    }

    /**
     * @param string $mailThemeFolder
     *
     * @throws TypeException
     *
     * @return LayoutCollectionInterface
     */
    private function findThemeLayouts($mailThemeFolder)
    {
        $mailThemeLayouts = new LayoutCollection();
        $this->addCoreLayouts($mailThemeLayouts, $mailThemeFolder);
        $this->addModulesLayouts($mailThemeLayouts, $mailThemeFolder);

        return $mailThemeLayouts;
    }

    /**
     * @param LayoutCollectionInterface $collection
     * @param string $mailThemeFolder
     */
    private function addCoreLayouts(LayoutCollectionInterface $collection, $mailThemeFolder)
    {
        $coreLayoutsFolder = implode(DIRECTORY_SEPARATOR, [
            $mailThemeFolder,
            MailTemplateInterface::CORE_CATEGORY,
        ]);
        if (!is_dir($coreLayoutsFolder)) {
            return;
        }

        $this->addLayoutsFromFolder($collection, $coreLayoutsFolder);
    }

    /**
     * @param LayoutCollectionInterface $collection
     * @param string $mailThemeFolder
     */
    private function addModulesLayouts(LayoutCollectionInterface $collection, $mailThemeFolder)
    {
        $moduleLayoutsFolder = implode(DIRECTORY_SEPARATOR, [
            $mailThemeFolder,
            MailTemplateInterface::MODULES_CATEGORY,
        ]);
        if (!is_dir($moduleLayoutsFolder)) {
            return;
        }

        $moduleFinder = new Finder();
        $moduleFinder->directories()->in($moduleLayoutsFolder)->depth(0);

        /* @var SplFileInfo $moduleFolder */
        foreach ($moduleFinder as $moduleFolder) {
            $this->addLayoutsFromFolder($collection, $moduleFolder->getRealPath(), $moduleFolder->getFilename());
        }
    }

    /**
     * @param LayoutCollectionInterface $collection
     * @param string $folder
     * @param string $moduleName
     */
    private function addLayoutsFromFolder(
        LayoutCollectionInterface $collection,
        $folder,
        $moduleName = ''
    ) {
        $layoutFiles = [];
        $finder = new Finder();
        $finder->files()->in($folder)->sortByName();
        /** @var SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            //Get filename without any extension (ex: account.html.twig -> account)
            $layoutName = preg_replace('/\..+/', '', $fileInfo->getBasename());
            if (!isset($layoutFiles[$layoutName])) {
                $layoutFiles[$layoutName] = [
                    MailTemplateInterface::HTML_TYPE => '',
                    MailTemplateInterface::TXT_TYPE => '',
                ];
            }
            $templateType = $this->getTemplateType($fileInfo);
            $layoutFiles[$layoutName][$templateType] = $this->getTemplatePath($fileInfo);
        }

        foreach ($layoutFiles as $layoutName => $layouts) {
            $collection->add(new Layout(
                $layoutName,
                $layouts[MailTemplateInterface::HTML_TYPE],
                $layouts[MailTemplateInterface::TXT_TYPE],
                $moduleName
            ));
        }
    }

    /**
     * @param SplFileInfo $fileInfo
     *
     * @return string
     */
    private function getTemplateType(SplFileInfo $fileInfo)
    {
        $ext = !empty($fileInfo->getExtension()) ? '.' . $fileInfo->getExtension() : '';
        $htmlTypeRegexp = sprintf('/.+\.%s%s/', MailTemplateInterface::HTML_TYPE, $ext);
        if (preg_match($htmlTypeRegexp, $fileInfo->getFilename())) {
            return MailTemplateInterface::HTML_TYPE;
        }

        return MailTemplateInterface::TXT_TYPE;
    }

    /**
     * @throws FileNotFoundException
     */
    private function checkThemeFolder($mailThemeFolder)
    {
        if (!is_dir($mailThemeFolder)) {
            throw new FileNotFoundException(sprintf('Invalid mail theme folder "%s": no such directory', $mailThemeFolder));
        }
    }

    private function getTemplatePath(SplFileInfo $fileInfo): string
    {
        return '@MailThemes' . substr($fileInfo->getRealPath(), strlen($this->baseThemeFolder));
    }
}
