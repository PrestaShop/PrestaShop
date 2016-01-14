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

class ThemeManager implements AddonManagerInterface
{
    private $shop;
    private $employee;
    private $theme_checker;
    private $configurator;
    private $themes;

    public function __construct(
        \Shop $shop,
        ConfigurationInterface $configurator,
        ThemeChecker $theme_checker,
        \Employee $employee = null)
    {
        $this->shop = $shop;
        $this->configurator = $configurator;
        $this->theme_checker = $theme_checker;
        $this->employee = $employee;
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
        if (!$this->theme_checker->setTheme($theme)->isValid()) {
            return false;
        }

        $this->disable($this->shop->theme_name);
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
        $theme = new Theme($dir);

        return $theme;
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
}
