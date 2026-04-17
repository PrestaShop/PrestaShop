<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorLanguageLoader
{
    public const TRANSLATION_DIR = _PS_ROOT_DIR_ . '/translations';
    private const MODULE_TRANSLATION_FILENAME_PATTERN = '#^%s[A-Z][\w.-]+\.%s\.xlf$#';

    /**
     * @var bool
     */
    private $isAdminContext = false;

    private XliffFileLoader $xliffFileLoader;

    public function __construct(
        private readonly ModuleRepository $moduleRepository,
    ) {
        $this->xliffFileLoader = new XliffFileLoader();
    }

    /**
     * @param bool $isAdminContext
     *
     * @return self
     */
    public function setIsAdminContext(bool $isAdminContext): self
    {
        $this->isAdminContext = $isAdminContext;

        return $this;
    }

    /**
     * Loads a language into a translator
     *
     * @param TranslatorInterface $translator Translator to modify
     * @param string $locale Locale code for the language to load
     * @param bool $withDB [default=true] Whether to load translations from the database or not
     * @param Theme|null $theme [default=false] Currently active theme (Front office only)
     */
    public function loadLanguage(TranslatorInterface $translator, $locale, $withDB = true, ?Theme $theme = null)
    {
        if (!method_exists($translator, 'isLanguageLoaded')) {
            return;
        }

        if (method_exists($translator, 'addLoader')) {
            $translator->addLoader('xlf', $this->xliffFileLoader);
            if ($withDB) {
                $translator->addLoader('db', new SqlTranslationLoader());
                if (null !== $theme) {
                    $sqlThemeTranslationLoader = new SqlTranslationLoader();
                    $sqlThemeTranslationLoader->setTheme($theme);
                    $translator->addLoader('db.theme', $sqlThemeTranslationLoader);
                }
            }
        }

        // Load the theme translations catalogue
        foreach ($this->getTranslationResourcesDirectories($theme) as $type => $directory) {
            $finder = Finder::create()
                ->files()
                ->name('*.' . $locale . '.xlf')
                ->notName($this->isAdminContext ? '^Shop*' : '^Admin*')
                ->followLinks()
                ->in($directory);

            foreach ($finder as $file) {
                [$domain, $locale, $format] = explode('.', $file->getBasename(), 3);
                if (method_exists($translator, 'addResource')) {
                    $translator->addResource($format, $file, $locale, $domain);
                    if ($withDB) {
                        if ($type !== 'theme') {
                            // Load core user-translated wordings
                            $translator->addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
                        }
                        if (!$this->isAdminContext && $theme !== null) {
                            // Load theme user-translated wordings for core + theme wordings
                            $translator->addResource('db.theme', $domain . '.' . $locale . '.db', $locale, $domain);
                        }
                    }
                } elseif ($translator instanceof TranslatorBagInterface) {
                    $catalogue = $translator->getCatalogue($locale);
                    $catalogue->addCatalogue($this->xliffFileLoader->load($file->getRealPath(), $locale, $domain));
                }
            }
        }

        // Load modules translation catalogues
        $activeModulesPaths = $this->moduleRepository->getPresentModulesPaths();
        foreach ($activeModulesPaths as $activeModuleName => $activeModulePath) {
            $this->loadModuleTranslations($translator, $activeModuleName, $activeModulePath, $locale, $withDB);
        }
    }

    /**
     * Loads translations for a single module
     */
    protected function loadModuleTranslations(
        TranslatorInterface $translator,
        string $moduleName,
        string $modulePath,
        string $locale,
        bool $withDB = true
    ): void {
        $translationDir = sprintf('%s/translations/%s', $modulePath, $locale);
        if (!is_dir($translationDir)) {
            return;
        }

        $filenamePattern = sprintf(
            self::MODULE_TRANSLATION_FILENAME_PATTERN,
            preg_quote(DomainHelper::buildModuleBaseDomain($moduleName)),
            $locale
        );
        $modulesCatalogueFinder = Finder::create()
            ->files()
            ->name($filenamePattern)
            ->followLinks()
            ->in($translationDir);

        foreach ($modulesCatalogueFinder as $file) {
            [$domain, $locale, $format] = explode('.', $file->getBasename(), 3);
            if (method_exists($translator, 'addResource')) {
                $translator->addResource($format, $file, $locale, $domain);
                if ($withDB) {
                    $translator->addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
                }
            } elseif ($translator instanceof TranslatorBagInterface) {
                $catalogue = $translator->getCatalogue($locale);
                $catalogue->addCatalogue($this->xliffFileLoader->load($file->getRealPath(), $locale, $domain));
            }
        }
    }

    /**
     * @param Theme|null $theme
     *
     * @return array
     */
    protected function getTranslationResourcesDirectories(?Theme $theme = null): array
    {
        $locations = ['core' => self::TRANSLATION_DIR];

        if (null !== $theme) {
            $activeThemeLocation = $theme->getDirectory() . '/translations';
            if (is_dir($activeThemeLocation)) {
                $locations['theme'] = $activeThemeLocation;
            }
        }

        return $locations;
    }
}
