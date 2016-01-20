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
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeChecker;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use \Tools;
use \Shop;
use \Employee;
use \Exception;

class ThemeManager implements AddonManagerInterface
{
    private $shop;
    private $employee;
    private $theme_checker;
    private $configurator;
    private $fs;
    private $finder;
    private $themes;

    public function __construct(
        Shop $shop,
        ConfigurationInterface $configurator,
        ThemeChecker $theme_checker,
        Employee $employee,
        Filesystem $fs = null,
        Finder $finder = null)
    {
        $this->shop = $shop;
        $this->configurator = $configurator;
        $this->theme_checker = $theme_checker;
        $this->employee = $employee;

        if (isset($fs)) {
            $this->fs = $fs;
        } else {
            $this->fs = new Filesystem();
        }

        if (isset($finder)) {
            $this->finder = $finder;
        } else {
            $this->finder = new Finder();
        }
    }

    /**
     * Add new theme from zipball. This will unzip the file and move the content
     * to the right locations.
     * A theme can bundle modules, resources, docuementation, email templates and so on.
     *
     * @param string $source The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
     * @return bool true for success
     */
    public function install($source)
    {
        if (preg_match('/\.zip$/', $source)) {
            $this->installFromZip($source);
        }
        return true;
    }

    /**
     * Remove all theme files, resources, documentation and specific modules
     *
     * @param Addon $theme The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
     * @return bool true for success
     */
    public function uninstall($name)
    {
        if (!$this->employee->canDelete()
            && $this->isThemeUsed($name)) {
            return false;
        }

        $theme = $this->getInstanceByName($name);
        $theme->onUninstall();

        $this->fs->remove($theme->directory);

        return true;
    }

    /**
    * Download new files from source, backup old files, replace files with new ones
    * and execute all necessary migration scripts form current version to the new one.
    *
    * @param Addon $theme the theme you want to upgrade
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
        if (!$this->employee->canEdit()) {
            return false;
        }

        $theme = $this->getInstanceByName($name);
        if (!$this->theme_checker->setTheme($theme->directory)->isValid()) {
            return false;
        }

        $this->disable($this->shop->theme_name);

        $this->doApplyConfiguration($theme->global_settings['configuration'])
                ->doDisableModules($theme->global_settings['modules']['toDisable'])
                ->doEnableModules($theme->global_settings['modules']['toEnable'])
                ->doHookModules($theme->global_settings['modules']['toHookOn']);

        $theme->onEnable();

        $this->shop->theme_name = $theme->name;
        $this->shop->update();

        return $this;
    }

    /**
     * Actions to perform when switchig from this theme to another one.
     *
     * @param  string $name The theme name to enable
     * @return bool         True for success
     */
    public function disable($name)
    {
        return true;
    }

    /**
     * Actions to perform to restaure default settings
     *
     * @param  string $name The theme name to reset
     * @return bool         True for success
     */
    public function reset($name)
    {
        return true;
    }

    public function getInstanceByName($name)
    {
        $dir = $this->configurator->get('_PS_ALL_THEMES_DIR_').$name;

        $data = $this->getConfigFromFile(
            $dir.'/config/theme.yml'
        );
        $data['directory'] = $dir;
        $data['settings'] = $this->getConfigFromFile(
            $dir.'/config/settings.json'
        );

        return new Theme($data);
    }

    public function getThemeList()
    {
        if (!isset($this->themes)) {
            $this->themes = $this->getAddonList(new AddonListFilter());
        }

        return $this->themes;
    }

    public function getThemeListExcluding(array $exclude)
    {
        $filter = (new AddonListFilter())
            ->setExclude($exclude);

        return $this->getAddonList($filter);
    }

    public function getAddonList(AddonListFilter $filter)
    {
        $filter->setType(AddonListFilterType::THEME);

        if (!isset($filter->status)) {
            $filter->setStatus(AddonListFilterStatus::ALL);
        }

        $themes = $this->getThemeOnDisk();

        foreach ($filter->exclude as $name) {
            unset($themes[$name]);
        }

        return $themes;
    }

    private function doApplyConfiguration(array $configuration)
    {
        foreach ($configuration as $key => $value) {
            $this->configurator->set($key, $value);
        }
        return $this;
    }

    private function doDisableModules(array $modules)
    {
        // TODO: implements doDisableModules
        return $this;
    }

    private function doEnableModules(array $modules)
    {
        // TODO: implements doEnableModules
        return $this;
    }

    private function doHookModules(array $hooks)
    {
        // TODO: implements doHookModules
        return $this;
    }

    private function getThemeOnDisk()
    {
        $suffix = 'preview.png';
        $all_theme_dirs = glob($this->configurator->get('_PS_ALL_THEMES_DIR_').'*/'.$suffix);

        $themes = [];
        foreach ($all_theme_dirs as $dir) {
            $name = basename(substr($dir, 0, -strlen($suffix)));
            $theme = $this->getInstanceByName($name);
            if (isset($theme)) {
                $themes[$name] = $theme;
            }
        }

        return $themes;
    }

    private function installFromZip($source)
    {
        $sandbox_path = $this->getSandboxPath();

        Tools::ZipExtract($source, $sandbox_path);

        $directories = $this->finder->directories()
                                    ->in($sandbox_path)
                                    ->depth('== 0')
                                    ->exclude(['__MACOSX']);

        if (iterator_count($directories->directories()) > 1) {
            $this->fs->remove($sandbox_path);
            throw new Exception("Invalid theme zip");
        }

        $directories = iterator_to_array($directories);
        $theme_name = basename(current($directories)->getFileName());

        $theme_data = Yaml::parse($sandbox_path.$theme_name.'/config/theme.yml');
        $theme_data['directory'] = $sandbox_path.$theme_name;
        if (!$this->theme_checker->setTheme(new Theme($theme_data))->isValid()) {
            $this->fs->remove($sandbox_path);
            throw new Exception("This theme is not valid for PrestaShop 1.7");
        }

        $modules_to_copy = $sandbox_path.$theme_name.'/dependencies/modules';
        if ($this->fs->exists($modules_to_copy)) {
            $this->fs->mirror(
                $modules_to_copy,
                $this->configurator->get('_PS_MODULE_DIR_')
            );
            $this->fs->remove($modules_to_copy);
        }

        $dest = $this->configurator->get('_PS_ALL_THEMES_DIR_').$theme_name;
        $this->fs->mkdir($dest);
        $this->fs->mirror(
            $sandbox_path.$theme_name,
            $dest
        );
        $this->fs->remove($sandbox_path);
    }

    private function getSandboxPath()
    {
        if (!isset($this->sandbox)) {
            $this->sandbox = $this->configurator->get('_PS_CACHE_DIR_').'sandbox/'.uniqid().'/';
            $this->fs->mkdir($this->sandbox, 0755);
        }
        return $this->sandbox;
    }

    private function getConfigFromFile($file)
    {
        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);

        if (preg_match('/.\.(yml|yaml)$/', $file)) {
            return Yaml::parse($content);
        } elseif (preg_match('/.\.json$/', $file)) {
            return json_decode($content, true);
        }
    }

    public function saveTheme($theme)
    {
        $test = file_put_contents(
            $theme->directory.'/config/settings.json',
            json_encode($theme->settings)
        );
    }
}
