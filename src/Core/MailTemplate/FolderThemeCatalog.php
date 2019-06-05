<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This is a basic mail layouts catalog, not a lot of intelligence it is based
 * simply on existing files on the $mailThemesFolder (no database, or config files).
 * If a module includes its own theme folder it can override the default one through
 * the hook:
 *  FolderThemeCatalog::GET_MAIL_THEME_FOLDER_HOOK => actionGetMailThemeFolder
 */
class FolderThemeCatalog implements ThemeCatalogInterface
{
    const GET_MAIL_THEME_FOLDER_HOOK = 'actionGetMailThemeFolder';

    /** @var string */
    private $mailThemesFolder;

    /** @var HookDispatcherInterface */
    private $hookDispatcher;

    /**
     * @param string $mailThemesFolder
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct($mailThemesFolder, HookDispatcherInterface $hookDispatcher)
    {
        $this->mailThemesFolder = $mailThemesFolder;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * Returns the list of found themes (non empty folders, in the mail themes
     * folder).
     *
     * @throws FileNotFoundException
     * @throws TypeException
     *
     * @return ThemeCollectionInterface
     */
    public function listThemes()
    {
        $this->checkThemesFolder();

        $finder = new Finder();
        $finder->directories()->in($this->mailThemesFolder)->depth(0);
        $mailThemes = new ThemeCollection();
        /** @var SplFileInfo $mailThemeFolder */
        foreach ($finder as $mailThemeFolder) {
            $dirFinder = new Finder();
            $dirFinder->files()->in($mailThemeFolder->getRealPath());
            if ($dirFinder->count() > 0) {
                $mailTheme = new Theme($mailThemeFolder->getFilename());
                $layouts = $this->findThemeLayouts($mailTheme->getName());
                $mailTheme->setLayouts($layouts);
                $mailThemes[] = $mailTheme;
            }
        }

        //This hook allows you to add/remove a mail theme
        $this->hookDispatcher->dispatchWithParameters(
            ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK,
            ['mailThemes' => $mailThemes]
        );

        return $mailThemes;
    }

    /**
     * @param string $theme
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws TypeException
     *
     * @return ThemeInterface
     */
    public function getByName($theme)
    {
        /** @var ThemeCollectionInterface $availableThemes */
        $availableThemes = $this->listThemes();
        $themeNames = [];
        /** @var ThemeInterface $availableTheme */
        foreach ($availableThemes as $availableTheme) {
            if ($theme === $availableTheme->getName()) {
                return $availableTheme;
            }
            $themeNames[] = $availableTheme->getName();
        }

        throw new InvalidArgumentException(sprintf(
            'Invalid requested theme "%s", only available themes are: %s',
            $theme,
            implode(', ', $themeNames)
        ));
    }

    /**
     * @param string $mailTheme
     *
     * @throws FileNotFoundException
     * @throws TypeException
     *
     * @return LayoutCollectionInterface
     */
    private function findThemeLayouts($mailTheme)
    {
        $mailThemeFolder = implode(DIRECTORY_SEPARATOR, [$this->mailThemesFolder, $mailTheme]);
        //This hook allows to change the mail them folder
        $this->hookDispatcher->dispatchWithParameters(
            static::GET_MAIL_THEME_FOLDER_HOOK,
            [
                'mailTheme' => $mailTheme,
                'mailThemeFolder' => &$mailThemeFolder,
            ]
        );
        $this->checkThemeFolder($mailThemeFolder);

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

        /* @var SplFileInfo $fileInfo */
        foreach ($moduleFinder as $moduleFileInfo) {
            $moduleName = $moduleFileInfo->getFilename();
            $moduleFolder = implode(DIRECTORY_SEPARATOR, [$moduleLayoutsFolder, $moduleName]);
            $this->addLayoutsFromFolder($collection, $moduleFolder, $moduleName);
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
            $layoutFiles[$layoutName][$templateType] = $fileInfo->getRealPath();
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
    private function checkThemesFolder()
    {
        if (!is_dir($this->mailThemesFolder)) {
            throw new FileNotFoundException(sprintf(
                'Invalid mail themes folder "%s": no such directory',
                $this->mailThemesFolder
            ));
        }
    }

    /**
     * @throws FileNotFoundException
     */
    private function checkThemeFolder($mailThemeFolder)
    {
        if (!is_dir($mailThemeFolder)) {
            throw new FileNotFoundException(sprintf(
                'Invalid mail theme folder "%s": no such directory',
                $mailThemeFolder
            ));
        }
    }
}
