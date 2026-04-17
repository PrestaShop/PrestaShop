<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return ThemeCollectionInterface
     *
     * @throws FileNotFoundException
     * @throws TypeException
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

        // This hook allows you to add/remove a mail theme
        $this->hookDispatcher->dispatchWithParameters(
            ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK,
            ['mailThemes' => $mailThemes]
        );

        return $mailThemes;
    }

    /**
     * @param string $theme
     *
     * @return ThemeInterface
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     * @throws TypeException
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
