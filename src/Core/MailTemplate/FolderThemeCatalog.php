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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This is a basic mail layouts catalog, not a lot of intelligence it is based
 * simply on existing files on the $mailThemesFolder (no database, or config files).
 */
final class FolderThemeCatalog implements ThemeCatalogInterface
{
    /** @var string */
    private $mailThemesFolder;

    /** @var HookDispatcherInterface */
    private $hookDispatcher;

    /** @var FolderThemeScanner */
    private $scanner;

    /**
     * @param string $mailThemesFolder
     * @param FolderThemeScanner $scanner
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        $mailThemesFolder,
        FolderThemeScanner $scanner,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->mailThemesFolder = $mailThemesFolder;
        $this->scanner = $scanner;
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
        $finder->sortByName();
        $finder->directories()->in($this->mailThemesFolder)->depth(0);
        $mailThemes = new ThemeCollection();
        /** @var SplFileInfo $mailThemeFolder */
        foreach ($finder as $mailThemeFolder) {
            $mailTheme = $this->scanner->scan($mailThemeFolder->getRealPath());
            if ($mailTheme->getLayouts()->count() > 0) {
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

        throw new InvalidArgumentException(sprintf('Invalid requested theme "%s", only available themes are: %s', $theme, implode(', ', $themeNames)));
    }

    /**
     * @throws FileNotFoundException
     */
    private function checkThemesFolder()
    {
        if (!is_dir($this->mailThemesFolder)) {
            throw new FileNotFoundException(sprintf('Invalid mail themes folder "%s": no such directory', $this->mailThemesFolder));
        }
    }
}
