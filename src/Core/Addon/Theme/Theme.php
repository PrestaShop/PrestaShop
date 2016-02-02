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

use PrestaShop\PrestaShop\Core\Addon\AddonInterface;
use Shudrum\Component\ArrayFinder\ArrayFinder;

class Theme implements AddonInterface
{
    private $attributes;

    public function __construct(array $attributes)
    {
        $attributes['directory'] = rtrim($attributes['directory'], '/') . '/';

        $this->attributes = new ArrayFinder($attributes);
    }

    public function get($attr, $default = null)
    {
        return $this->attributes->get($attr, $default);
    }

    public function getName()
    {
        return $this->attributes->get('name');
    }

    public function getDirectory()
    {
        return $this->attributes->get('directory');
    }

    public function onInstall()
    {
        return true;
    }

    public function onUninstall()
    {
        return true;
    }

    /**
    * Execute up files. You can update configuration, update sql schema.
    * No file modification.
    *
    * @return bool true for success
    */
    public function onUpgrade($version)
    {
        return true;
    }

    /**
     * Called when switching the current theme of the selected shop.
     * You can update configuration, enable/disable modules...
     *
     * @return bool true for success
     */
    public function onEnable()
    {
        return true;
    }

    /**
     * Not necessarily the opposite of enable. Use this method if
     * something must be done when switching to another theme (like uninstall
     * very specific modules for example)
     *
     * @return bool true for success
     */
    public function onDisable()
    {
        return true;
    }

    public function onReset()
    {
        return true;
    }

    public function setPageLayouts(array $layouts)
    {
        $this->attributes->set('settings.page_layouts', $layouts);
    }

    public function getLayoutForPage($page)
    {
        $layout_name = $this->get('theme_settings.default_layout');
        if (isset($this->attributes['settings']['page_layouts'][$page])
            && $this->attributes['settings']['page_layouts'][$page]) {
            $layout_name = $this->attributes['settings']['page_layouts'][$page];
        }

        $filename = 'layouts/'.$layout_name.'.tpl';
        return $filename;
    }
}
