<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Configuration\AdvancedConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\Provider\TranslationFinderTrait;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;
use Tools;
use Shop;
use Employee;
use Exception;
use PrestaShopException;
use PrestaShopLogger;
use Language;

class ThemeManager implements AddonManagerInterface
{
    use TranslationFinderTrait;

    private $hookConfigurator;
    private $shop;
    private $employee;
    private $themeValidator;
    private $appConfiguration;
    private $filesystem;
    private $finder;
    private $themeRepository;

    public function __construct(
        Shop $shop,
        AdvancedConfigurationInterface $configuration,
        ThemeValidator $themeValidator,
        TranslatorInterface $translator,
        Employee $employee,
        Filesystem $filesystem,
        Finder $finder,
        HookConfigurator $hookConfigurator,
        ThemeRepository $themeRepository,
        ImageTypeRepository $imageTypeRepository
    ) {
        $this->shop = $shop;
        $this->appConfiguration = $configuration;
        $this->themeValidator = $themeValidator;
        $this->translator = $translator;
        $this->employee = $employee;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->hookConfigurator = $hookConfigurator;
        $this->themeRepository = $themeRepository;
        $this->imageTypeRepository = $imageTypeRepository;
    }

    /**
     * Add new theme from zipball. This will unzip the file and move the content
     * to the right locations.
     * A theme can bundle modules, resources, documentation, email templates and so on.
     *
     * @param string $source The source can be a module name (installed from either local disk or addons.prestashop.com).
     *                       or a location (url or path to the zip file)
     *
     * @return bool true for success
     */
    public function install($source)
    {
        if ((filter_var($source, FILTER_VALIDATE_URL))) {
            $source = Tools::createFileFromUrl($source);
        }
        if (preg_match('/\.zip$/', $source)) {
            $this->installFromZip($source);
        }

        return true;
    }

    /**
     * Remove all theme files, resources, documentation and specific modules.
     *
     * @param $name The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
     *
     * @return bool true for success
     */
    public function uninstall($name)
    {
        if (!$this->employee->can('delete', 'AdminThemes')
            && $this->isThemeUsed($name)) {
            return false;
        }

        $theme = $this->themeRepository->getInstanceByName($name);
        $theme->onUninstall();

        $this->filesystem->remove($theme->getDirectory());

        return true;
    }

    /**
     * Download new files from source, backup old files, replace files with new ones
     * and execute all necessary migration scripts form current version to the new one.
     *
     * @param string $name
     * @param string $version the version you want to up upgrade to
     * @param string $source  if the upgrade is not coming from addons, you need to specify the path to the zipball
     *
     * @return bool true for success
     */
    public function upgrade($name, $version, $source = null)
    {
        return true;
    }

    /**
     * Actions to perform when switching from another theme to this one.
     * Example:
     *    - update configuration
     *    - enable/disable modules.
     *
     * @param string $name The theme name to enable
     *
     * @param bool $force bypass user privilege checks
     * @return bool True for success
     */
    public function enable($name, $force = false)
    {
        if (!$force && !$this->employee->can('edit', 'AdminThemes')) {
            return false;
        }

        /* if file exits, remove it and use YAML configuration file instead */
        @unlink($this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$name.'/shop'.$this->shop->id.'.json');

        $theme = $this->themeRepository->getInstanceByName($name);
        if (!$this->themeValidator->isValid($theme)) {
            return false;
        }

        $this->disable($this->shop->theme_name);

        $this->doCreateCustomHooks($theme->get('global_settings.hooks.custom_hooks', array()))
                ->doApplyConfiguration($theme->get('global_settings.configuration', array()))
                ->doDisableModules($theme->get('global_settings.modules.to_disable', array()))
                ->doEnableModules($theme->getModulesToEnable())
                ->doResetModules($theme->get('global_settings.modules.to_reset', array()))
                ->doApplyImageTypes($theme->get('global_settings.image_types'))
                ->doHookModules($theme->get('global_settings.hooks.modules_to_hook'));

        $theme->onEnable();

        $this->shop->theme_name = $theme->getName();
        $this->shop->update();

        $this->saveTheme($theme);

        return $this;
    }

    /**
     * Actions to perform when switching from this theme to another one.
     *
     * @param string $name The theme name to enable
     *
     * @return bool True for success
     */
    public function disable($name)
    {
        $theme = $this->themeRepository->getInstanceByName($name);
        $theme->getModulesToDisable();

        $this->doDisableModules($theme->getModulesToDisable());

        @unlink($this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$name.'/shop'.$this->shop->id.'.json');

        return true;
    }

    /**
     * Actions to perform to restore default settings.
     *
     * @param string $themeName The theme name to reset
     *
     * @return bool True for success
     */
    public function reset($themeName)
    {
        return $this->disable($themeName) && $this->enable($themeName);
    }

    /**
     * Returns the last error, if found.
     *
     * @param string $themeName The technical theme name
     *
     * @return string|null The last error if found
     */
    public function getError($themeName)
    {
        return;
    }

    /**
     * Get all errors of theme install
     *
     * @param string $themeName The technical theme name
     * @return array|false
     */
    public function getErrors($themeName)
    {
        return $this->themeValidator->getErrors($themeName);
    }

    private function doCreateCustomHooks(array $hooks)
    {
        foreach ($hooks as $hook) {
            $this->hookConfigurator->addHook(
                $hook['name'],
                $hook['title'],
                $hook['description']
            );
        }

        return $this;
    }

    private function doApplyConfiguration(array $configuration)
    {
        foreach ($configuration as $key => $value) {
            $this->appConfiguration->set($key, $value);
        }

        return $this;
    }

    private function doDisableModules(array $modules)
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        foreach ($modules as $key => $moduleName) {
            if ($moduleManager->isInstalled($moduleName) && $moduleManager->isEnabled($moduleName)) {
                $moduleManager->disable($moduleName);
            }
        }

        return $this;
    }

    private function doEnableModules(array $modules)
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build()->setActionParams(['confirmPrestaTrust' => true]);

        foreach ($modules as $key => $moduleName) {
            if (!$moduleManager->isInstalled($moduleName)
                && !$moduleManager->install($moduleName)
            ) {
                throw new PrestaShopException(
                    $this->translator->trans(
                        'Cannot %action% module %module%. %error_details%',
                        array(
                            '%action%' => 'install',
                            '%module%' => $moduleName,
                            '%error_details%' => $moduleManager->getError($moduleName),
                        ),
                        'Admin.Modules.Notification')
                );
            }
            if (!$moduleManager->isEnabled($moduleName)) {
                $moduleManager->enable($moduleName);
            }
        }

        return $this;
    }

    /**
     * Reset the modules received in parameters if they are installed and enabled
     *
     * @param string[] $modules
     * @return $this
     */
    private function doResetModules(array $modules)
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        foreach ($modules as $moduleName) {
            if ($moduleManager->isInstalled($moduleName)) {
                $moduleManager->reset($moduleName);
            }
        }

        return $this;
    }

    private function doHookModules(array $hooks)
    {
        $this->hookConfigurator->setHooksConfiguration($hooks);

        return $this;
    }

    private function doApplyImageTypes(array $types)
    {
        $this->imageTypeRepository->setTypes($types);

        return $this;
    }

    private function installFromZip($source)
    {
        $finderClass = get_class($this->finder);
        $this->finder = $finderClass::create();

        $sandboxPath = $this->getSandboxPath();
        Tools::ZipExtract($source, $sandboxPath);

        $theme_data = (new Parser())->parse(file_get_contents($sandboxPath.'/config/theme.yml'));
        $theme_data['directory'] = $sandboxPath;
        $theme = new Theme($theme_data);
        if (!$this->themeValidator->isValid($theme)) {
            $this->filesystem->remove($sandboxPath);
            throw new PrestaShopException(
                $this->translator->trans('This theme is not valid for PrestaShop 1.7', [], 'Admin.Design.Notification')
            );
        }

        $module_root_dir = $this->appConfiguration->get('_PS_MODULE_DIR_');
        $modules_parent_dir = $sandboxPath.'/dependencies/modules';
        if ($this->filesystem->exists($modules_parent_dir)) {
            $module_dirs = $this->finder->directories()
                                        ->in($modules_parent_dir)
                                        ->depth('== 0');

            foreach (iterator_to_array($module_dirs) as $dir) {
                $destination = $module_root_dir.basename($dir->getFileName());
                if (!$this->filesystem->exists($destination)) {
                    $this->filesystem->mkdir($destination);
                }
                $this->filesystem->mirror($dir->getPathName(), $destination);
            }
            $this->filesystem->remove($modules_parent_dir);
        }

        $themePath = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$theme->getName();
        if ($this->filesystem->exists($themePath)) {
            throw new PrestaShopException(
                $this->translator->trans(
                    'There is already a theme named '
                    .$theme->getName()
                    .' in your themes/ folder. Remove it if you want to continue.',
                    [],
                    'Admin.Design.Notification'
                )
            );
        }

        $this->filesystem->mkdir($themePath);
        $this->filesystem->mirror($sandboxPath, $themePath);

        $this->importTranslationToDatabase($theme);

        $this->filesystem->remove($sandboxPath);
    }

    private function getSandboxPath()
    {
        if (!isset($this->sandbox)) {
            $this->sandbox = $this->appConfiguration->get('_PS_CACHE_DIR_').'sandbox/'.uniqid().'/';
            $this->filesystem->mkdir($this->sandbox, 0755);
        }

        return $this->sandbox;
    }

    /**
     * @param Theme $theme
     */
    public function saveTheme($theme)
    {
        $jsonConfigFolder = $this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$theme->getName();
        if (!$this->filesystem->exists($jsonConfigFolder) && !is_dir($jsonConfigFolder)) {
            mkdir($jsonConfigFolder, 0777, true);
        }

        file_put_contents(
            $jsonConfigFolder.'/shop'.$this->shop->id.'.json',
            json_encode($theme->get(null))
        );
    }

    /**
     * Import translation from Theme to Database
     *
     * @param Theme $theme
     */
    private function importTranslationToDatabase(Theme $theme)
    {
        global $kernel; // sf kernel

        if (!(!is_null($kernel) && $kernel instanceof \Symfony\Component\HttpKernel\KernelInterface)) {
            return;
        }

        $translationService = $kernel->getContainer()->get('prestashop.service.translation');
        $themeProvider = $kernel->getContainer()->get('prestashop.translation.theme_provider');

        $themeName = $theme->getName();
        $themePath = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$themeName;
        $translationFolder = $themePath.DIRECTORY_SEPARATOR.'translations'.DIRECTORY_SEPARATOR;

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $locale = $language['locale'];

            // retrieve Lang doctrine entity
            try {
                $lang = $translationService->findLanguageByLocale($locale);
            } catch (Exception $exception) {
                PrestaShopLogger::addLog('ThemeManager->importTranslationToDatabase() - Locale ' . $locale . ' does not exists');
                continue;
            }

            // check if translation dir for this lang exists
            if (!is_dir($translationFolder . $locale)) {
                continue;
            }

            // construct a new catalog for this lang and import in database if key and message are different
            $messageCatalog = $this->getCatalogueFromPaths($translationFolder . $locale, $locale);

            // get all default domain from catalog
            $allDomains = $this->getDefaultDomains($locale, $themeProvider);

            // do the import
            $this->handleImport($translationService, $messageCatalog, $allDomains, $lang, $locale, $themeName);
        }
    }

    /**
     * Get all default domain from catalog
     *
     * @param string $locale
     * @param \PrestaShopBundle\Translation\Provider\ThemeProvider $themeProvider
     *
     * @return array
     */
    private function getDefaultDomains($locale, $themeProvider)
    {
        $allDomains = array();

        $defaultCatalogue = $themeProvider
            ->setLocale($locale)
            ->getDefaultCatalogue()
        ;

        if (empty($defaultCatalogue)) {
            return $allDomains;
        }

        $defaultCatalogue = $defaultCatalogue->all();

        if (empty($defaultCatalogue)) {
            return $allDomains;
        }

        foreach (array_keys($defaultCatalogue) as $domain) {
            // AdminCatalogFeature.fr-FR to AdminCatalogFeature
            $domain = str_replace('.' . $locale, '', $domain);

            $allDomains[] = $domain;
        }

        return $allDomains;
    }

    /**
     * @param TranslationService $translationService
     * @param MessageCatalogue $messageCatalog
     * @param array $allDomains
     * @param \PrestaShopBundle\Entity\Lang $lang
     * @param string $locale
     * @param string $themeName
     */
    private function handleImport(TranslationService $translationService, MessageCatalogue $messageCatalog, $allDomains, $lang, $locale, $themeName)
    {
        foreach ($messageCatalog->all() as $domain => $messages) {
            $domain = str_replace('.' . $locale, '', $domain);

            if (in_array($domain, $allDomains)) {
                continue;
            }

            foreach ($messages as $key => $message) {
                if ($key !== $message) {
                    $translationService->saveTranslationMessage($lang, $domain, $key, $message, $themeName);
                }
            }
        }
    }
}
