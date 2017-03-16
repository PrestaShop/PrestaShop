<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\Module\AddonListFilterDeviceStatus;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;

/**
 * This class is the interface to the legacy Module class.
 *
 * It will allow current modules to work even with the new ModuleManager
 */
class Module implements ModuleInterface
{
    /** @var legacyInstance Module The instance of the legacy module */
    public $instance = null;

    /**
     * Module attributes (name, displayName etc.).
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $attributes;

    /**
     * Module attributes from disk.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $disk;

    /**
     * Module attributes from database.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $database;

    /**
     * Default values for ParameterBag attributes.
     *
     * @var array
     */
    private $attributes_default = array(
        'id' => 0,
        'name' => '',
        'categoryName' => '',
        'displayName' => '',
        'version' => null,
        'description' => '',
        'author' => '',
        'author_uri' => false,
        'tab' => 'others',
        'is_configurable' => 0,
        'need_instance' => 0,
        'limited_countries' => array(),
        'parent_class' => 'Module',
        'productType' => 'module',
        'warning' => '',
        'img' => '',
        'badges' => array(),
        'cover' => array(),
        'screenshotsUrls' => array(),
        'videoUrl' => null,
        'refs' => array('unknown'),
        'price' => array(
            'EUR' => 0,
            'USD' => 0,
            'GBP' => 0,
        ),
        'type' => '',
        // From the marketplace
        'url' => null,
        'avgRate' => 0,
        'nbRates' => 0,
        'fullDescription' => '',
    );

    /**
     * Default values for ParameterBag disk.
     *
     * @var array
     */
    private $disk_default = array(
        'filemtype' => 0,
        'is_present' => 0,
        'is_valid' => 0,
        'version' => null,
    );

    /**
     * Default values for ParameterBag database.
     *
     * @var array
     */
    private $database_default = array(
        'installed' => 0,
        'active' => 0,
        'active_on_mobile' => true,
        'version' => null,
        'last_access_date' => '0000-00-00 00:00:00',
        'date_add' => null,
        'date_upd' => null,
    );

    /**
     * @param array $attributes
     * @param array $disk
     * @param array $database
     */
    public function __construct(array $attributes = array(), array $disk = array(), array $database = array())
    {
        $this->attributes = new ParameterBag($this->attributes_default);
        $this->disk = new ParameterBag($this->disk_default);
        $this->database = new ParameterBag($this->database_default);
        // Set all attributes
        $this->attributes->add($attributes);
        $this->disk->add($disk);
        $this->database->add($database);

        $version = is_null($this->attributes->get('version')) && $this->disk->get('is_valid') ?
            $this->disk->get('version') :
            $this->attributes->get('version');

        $img = $this->attributes->get('img');
        if (empty($img)) {
            $this->attributes->set('img', __PS_BASE_URI__.'img/questionmark.png');
        }

        $this->attributes->set('version', $version);
        $this->attributes->set('type', $this->convertType($this->get('origin_filter_value')));

        // Unfortunately, we can sometime have an array, and sometimes an object.
        // This is the first place where this value *always* exists
        $this->attributes->set('price', (array) $this->attributes->get('price'));
    }

    public function getInstance()
    {
        if (!$this->hasValidInstance()) {
            return;
        }

        return $this->instance;
    }

    /**
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

        // If not modified, code used in installer is executed:
        // "Notice: Use of undefined constant _PS_INSTALL_LANGS_PATH_ - assumed '_PS_INSTALL_LANGS_PATH_'"
        \Module::updateTranslationsAfterInstall(false);

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
     * very specific modules for example).
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

    public function onMobileEnable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        return $this->instance->enableDevice(AddonListFilterDeviceStatus::DEVICE_MOBILE);
    }

    public function onMobileDisable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        return $this->instance->disableDevice(AddonListFilterDeviceStatus::DEVICE_MOBILE);
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
        require_once _PS_MODULE_DIR_.DIRECTORY_SEPARATOR.$this->attributes->get('name').DIRECTORY_SEPARATOR.$this->attributes->get('name').'.php';
        $this->instance = \Module::getInstanceByName($this->attributes->get('name'));
    }

    public function get($attribute)
    {
        return $this->attributes->get($attribute, null);
    }

    public function set($attribute, $value)
    {
        $this->attributes->set($attribute, $value);
    }

    private function convertType($value)
    {
        $conversionTable = array(
            AddonListFilterOrigin::ADDONS_CUSTOMER => 'addonsBought',
            AddonListFilterOrigin::ADDONS_MUST_HAVE => 'addonsMustHave',
        );

        return isset($conversionTable[$value]) ? $conversionTable[$value] : '';
    }

    public function fillLogo()
    {
        $this->set('logo', '../../img/questionmark.png');

        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$this->get('name')
            .DIRECTORY_SEPARATOR.'logo.gif')) {
            $this->set('logo', 'logo.gif');
        }
        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$this->get('name')
            .DIRECTORY_SEPARATOR.'logo.png')) {
            $this->set('logo', 'logo.png');
        }
    }

    public function canBeUpgraded()
    {
        return
            $this->database->get('installed') == 1
            && $this->database->get('version')
            !== 0 && version_compare($this->database->get('version'), $this->attributes->get('version'), '<')
        ;
    }
}
