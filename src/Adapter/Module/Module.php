<?php
/*
 * 2007-2016 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\AddonInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This class is the interface to the legacy Module class.
 *
 * It will allow current modules to work even with the new ModuleManager
 */
class Module implements AddonInterface
{
    /** @var legacyInstance Module The instance of the legacy module */
    public $instance = null;

    /**
     * Module attributes (name, displayName etc.)
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $attributes;

    /**
     * Module attributes from disk
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $disk;

    /**
     * Module attributes from database
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $database;

    /**
     *
     * @param array $attributes
     * @param array $disk
     * @param array $database
     */
    public function __construct(array $attributes = [], array $disk = [], array $database = [])
    {
        // Set all attributes
        $this->attributes = new ParameterBag($attributes);
        $this->disk = new ParameterBag($disk);
        $this->database = new ParameterBag($database);
    }

    public function getInstance()
    {
        if (!$this->hasValidInstance()) {
            return null;
        }

        return $this->instance;
    }

    /**
     *
     * @return bool True if valid Module instance
     */
    public function hasValidInstance()
    {
        if (($this->disk->has('is_present') && $this->disk->get('is_present') == false)
            || ($this->disk->has('is_valid') && $this->disk->get('is_valid') == false)) {
            return false;
        }

        if ($this->instance === null) {
            // We try to instanciate the legacy class if not done yet
            try {
                $this->instanciateLegacyModule($this->attributes->get('name'));
            } catch (\Exception $e) {
                // ToDo: Send to log when PR merged
            }
        }
        $this->disk->set('is_valid', ($this->instance instanceof \ModuleCore));
        return $this->disk->get('is_valid');
    }

    public function onInstall()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }
        return $this->instance->install();
    }

    public function onUninstall()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }
        return $this->instance->uninstall();
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
        if (!$this->hasValidInstance()) {
            return false;
        }
        return $this->instance->enable();
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
        if (!$this->hasValidInstance()) {
            return false;
        }

        return $this->instance->disable();
    }

    public function onReset()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }
        return $this->instance->reset();
    }

    protected function instanciateLegacyModule()
    {
        require_once _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->attributes->get('name') . DIRECTORY_SEPARATOR . $this->attributes->get('name') . '.php';
        $this->instance = \Module::getInstanceByName($this->attributes->get('name'));
    }
}
