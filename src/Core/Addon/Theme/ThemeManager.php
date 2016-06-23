<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeChecker;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;
use \Tools;
use \Shop;
use \Employee;
use \Exception;

class ThemeManager implements AddonManagerInterface
{
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
        ConfigurationInterface $configuration,
        ThemeValidator $themeValidator,
        Employee $employee,
        Filesystem $filesystem,
        Finder $finder,
        HookConfigurator $hookConfigurator,
        ThemeRepository $themeRepository,
        ImageTypeRepository $imageTypeRepository)
    {
        $this->shop = $shop;
        $this->appConfiguration = $configuration;
        $this->themeValidator = $themeValidator;
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
     * or a location (url or path to the zip file)
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
     * Remove all theme files, resources, documentation and specific modules
     *
     * @param $name The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
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
     * @param string $source if the upgrade is not coming from addons, you need to specify the path to the zipball
     * @return bool true for success
     */
    public function upgrade($name, $version, $source = null)
    {
        return true;
    }

    /**
     * Actions to perform when switching from another theme to this one.
     * Example:
     * 	- update configuration
     * 	- enable/disable modules
     *
     * @param  string $name The theme name to enable
     * @return bool         True for success
     */
    public function enable($name)
    {
        if (!$this->employee->can('edit', 'AdminThemes')) {
            return false;
        }

        /* if file exits, remove it and use YAML configuration file instead */
        @unlink($this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$name .'/shop'.$this->shop->id.'.json');

        $theme = $this->themeRepository->getInstanceByName($name);
        if (!$this->themeValidator->isValid($theme)) {
            return false;
        }

        $this->disable($this->shop->theme_name);

        $this->doCreateCustomHooks($theme->get('global_settings.hooks.custom_hooks', []))
                ->doApplyConfiguration($theme->get('global_settings.configuration', []))
                ->doDisableModules($theme->get('global_settings.modules.to_disable', []))
                ->doEnableModules($theme->getModulesToEnable())
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
     * @param  string $name The theme name to enable
     * @return bool         True for success
     */
    public function disable($name)
    {
        return true;
    }

    /**
     * Actions to perform to restore default settings
     *
     * @param  string $theme_name The theme name to reset
     * @return bool         True for success
     */
    public function reset($theme_name)
    {
        return $this->disable($theme_name) && $this->enable($theme_name);
    }

    /**
     * Returns the last error, if found
     *
     * @param string $name The technical theme name
     * @return string|null The last error if found
     */
    public function getError($theme_name)
    {
        return null;
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
        $moduleManagerBuilder = new ModuleManagerBuilder();
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
        $moduleManagerBuilder = new ModuleManagerBuilder();
        $moduleManager = $moduleManagerBuilder->build();


        foreach ($modules as $key => $moduleName) {
            if (!$moduleManager->isInstalled($moduleName)) {
                $moduleManager->install($moduleName);
            }
            if (!$moduleManager->isEnabled($moduleName)) {
                $moduleManager->enable($moduleName);
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
        $sandboxPath = $this->getSandboxPath();

        Tools::ZipExtract($source, $sandboxPath);

        $directories = $this->finder->directories()
                                    ->in($sandboxPath)
                                    ->depth('== 0')
                                    ->exclude(['__MACOSX'])
                                    ->ignoreVCS(true);

        if (iterator_count($directories->directories()) > 1) {
            $this->filesystem->remove($sandboxPath);
            throw new Exception("Invalid theme zip");
        }

        $directories = iterator_to_array($directories);
        $theme_name = basename(current($directories)->getFileName());

        $theme_data = (new Parser())->parse(file_get_contents($sandboxPath.$theme_name.'/config/theme.yml'));
        $theme_data['directory'] = $sandboxPath.$theme_name;
        if (!$this->themeValidator->isValid(new Theme($theme_data))) {
            $this->filesystem->remove($sandboxPath);
            throw new Exception("This theme is not valid for PrestaShop 1.7");
        }

        $module_root_dir = $this->appConfiguration->get('_PS_MODULE_DIR_');
        $modules_parent_dir = $sandboxPath.$theme_name.'/dependencies/modules';
        if ($this->filesystem->exists($modules_parent_dir)) {
            $module_dirs = $this->finder->directories()
                                        ->in($modules_parent_dir)
                                        ->depth('== 0')
                                        ->exclude($theme_name);

            foreach (iterator_to_array($module_dirs) as $dir) {
                $destination = $module_root_dir.basename($dir->getFileName());
                if (!$this->fs->exists($destination)) {
                    $this->fs->mkdir($destination);
                    $this->fs->mirror(
                        $dir->getPathName(),
                        $destination
                    );
                }
            }
            $this->fs->remove($modules_parent_dir);
        }

        $themePath = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$theme_name;
        $this->filesystem->mkdir($themePath);
        $this->filesystem->mirror(
            $sandboxPath.$theme_name,
            $themePath
        );
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

    public function saveTheme($theme)
    {
        $jsonConfigFolder = $this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$theme->getName();
        if (!file_exists($jsonConfigFolder) && !is_dir($jsonConfigFolder)) {
            mkdir($jsonConfigFolder, 0777, true);
        }

        file_put_contents(
            $jsonConfigFolder.'/shop'.$this->shop->id.'.json',
            json_encode($theme->get(null))
        );
    }
}
