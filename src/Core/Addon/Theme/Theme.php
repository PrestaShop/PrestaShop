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

use Symfony\Component\Yaml\Parser;
use PrestaShop\PrestaShop\Core\Addon\AddonInterface;

class Theme implements AddonInterface
{
    private $yaml;

    public function __construct($directory)
    {
        $this->directory = rtrim($directory, '/') . '/';

        $this->yaml = new Parser();

        $this->setProperties();
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

    public function setProperties()
    {
        $properties = $this->getConfigFromFile('theme.yml');
        $properties['settings'] = $this->getConfigFromFile('settings.json');

        foreach ($properties as $prop => $value) {
            $this->{$prop} = $value;
        }

        return $this;
    }

    public function initSettings()
    {
        $this->settings = [];
        $this->settings['page_layouts'] = [];

        return $this->saveSettings();
    }

    public function setPageLayouts(array $layouts)
    {
        $this->settings['page_layouts'] = $layouts;
        $this->saveSettings();
    }

    private function getConfigFromFile($filename)
    {
        $file = $this->directory.'/config/'.$filename;
        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);

        if (preg_match('/.\.(yml|yaml)$/', $file)) {
            return $this->yaml->parse($content);
        } elseif (preg_match('/.\.json$/', $file)) {
            return json_decode($content, true);
        }
    }

    public function saveSettings()
    {
        return file_put_contents(
            $this->directory.'/config/settings.json',
            json_encode($this->settings)
        );
    }
}
