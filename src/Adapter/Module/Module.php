<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\Module\AddonListFilterDeviceStatus;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use Module as LegacyModule;

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
        'is_paymentModule' => false,
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
        'confirmUninstall' => '',
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
        'path' => '',
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

        if ($this->database->get('installed')) {
            $version = $this->database->get('version');
        } elseif (is_null($this->attributes->get('version')) && $this->disk->get('is_valid')) {
            $version = $this->disk->get('version');
        } else {
            $version = $this->attributes->get('version');
        }

        if (!$this->attributes->has('version_available')) {
            $this->attributes->set('version_available', $this->disk->get('version'));
        }

        $this->fillLogo();

        $this->attributes->set('version', $version);
        $this->attributes->set('type', $this->convertType($this->get('origin_filter_value')));

        // Unfortunately, we can sometime have an array, and sometimes an object.
        // This is the first place where this value *always* exists
        $this->attributes->set('price', (array) $this->attributes->get('price'));
    }

    /**
     * @return legacyInstance|void
     *
     * @throws \Exception
     */
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
        if (($this->disk->has('is_present') && $this->disk->getBoolean('is_present') === false)
            || ($this->disk->has('is_valid') && $this->disk->getBoolean('is_valid') === false)
        ) {
            return false;
        }

        if ($this->instance === null) {
            // We try to instantiate the legacy class if not done yet
            try {
                $this->instanciateLegacyModule($this->attributes->get('name'));
            } catch (\Exception $e) {
                $this->disk->set('is_valid', false);

                return false;
            }
        }

        $this->disk->set('is_valid', $this->instance instanceof LegacyModule);

        return $this->disk->get('is_valid');
    }

    /**
     * {@inheritdoc}
     */
    public function onInstall()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        // If not modified, code used in installer is executed:
        // "Notice: Use of undefined constant _PS_INSTALL_LANGS_PATH_ - assumed '_PS_INSTALL_LANGS_PATH_'"
        LegacyModule::updateTranslationsAfterInstall(false);

        $result = $this->instance->install();
        $this->database->set('installed', $result);
        $this->database->set('active', $result);
        $this->database->set('version', $this->attributes->get('version'));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onUninstall()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        $result = $this->instance->uninstall();
        $this->database->set('installed', !$result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onUpgrade($version)
    {
        $this->database->set('version', $this->attributes->get('version_available'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onEnable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        $result = $this->instance->enable();
        $this->database->set('active', $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onDisable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        $result = $this->instance->disable();
        $this->database->set('active', !$result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onMobileEnable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        $result = $this->instance->enableDevice(AddonListFilterDeviceStatus::DEVICE_MOBILE);
        $this->database->set('active_on_mobile', $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onMobileDisable()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        $result = $this->instance->disableDevice(AddonListFilterDeviceStatus::DEVICE_MOBILE);
        $this->database->set('active_on_mobile', !$result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function onReset()
    {
        if (!$this->hasValidInstance()) {
            return false;
        }

        return $this->instance->reset();
    }

    /**
     * Retrieve an instance of Legacy Module Object model from data.
     */
    protected function instanciateLegacyModule()
    {
        /*
         * @TODO Temporary: This test prevents an error when switching branches with the cache.
         * Can be removed at the next release (when we will be sure that it is defined)
         */
        $path = $this->disk->get('path', ''); // Variable needed for empty() test
        if (empty($path)) {
            $this->disk->set('path', _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->attributes->get('name'));
        }
        // End of temporary content
        require_once $this->disk->get('path') . DIRECTORY_SEPARATOR . $this->attributes->get('name') . '.php';
        $this->instance = LegacyModule::getInstanceByName($this->attributes->get('name'));
    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    public function get($attribute)
    {
        return $this->attributes->get($attribute, null);
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function set($attribute, $value)
    {
        $this->attributes->set($attribute, $value);
    }

    /**
     * @param $value
     *
     * @return mixed|string
     */
    private function convertType($value)
    {
        $conversionTable = array(
            AddonListFilterOrigin::ADDONS_CUSTOMER => 'addonsBought',
            AddonListFilterOrigin::ADDONS_MUST_HAVE => 'addonsMustHave',
        );

        return isset($conversionTable[$value]) ? $conversionTable[$value] : '';
    }

    /**
     * Set the module logo.
     */
    public function fillLogo()
    {
        $img = $this->attributes->get('img');
        if (empty($img)) {
            $this->attributes->set('img', __PS_BASE_URI__ . 'img/questionmark.png');
        }
        $this->attributes->set('logo', __PS_BASE_URI__ . 'img/questionmark.png');

        foreach (array('logo.png', 'logo.gif') as $logo) {
            $logo_path = _PS_MODULE_DIR_ . $this->get('name') . DIRECTORY_SEPARATOR . $logo;
            if (file_exists($logo_path)) {
                $this->attributes->set('img', __PS_BASE_URI__ . basename(_PS_MODULE_DIR_) . '/' . $this->get('name') . '/' . $logo);
                $this->attributes->set('logo', $logo);
                break;
            }
        }
    }

    /**
     * Inform the merchant an upgrade is wating to be applied from the disk or the marketplace.
     *
     * @return bool
     */
    public function canBeUpgraded()
    {
        if ($this->database->get('installed') == 0) {
            return false;
        }

        // Potential update from API
        if ($this->canBeUpgradedFromAddons()) {
            return true;
        }

        // Potential update from disk
        return version_compare($this->database->get('version'), $this->disk->get('version'), '<');
    }

    /**
     * Only check if an upgrade is available on the marketplace.
     *
     * @return bool
     */
    public function canBeUpgradedFromAddons()
    {
        return $this->attributes->get('version_available') !== 0
            && version_compare($this->database->get('version'), $this->attributes->get('version_available'), '<');
    }

    /**
     * Return installed modules
     *
     * @param int $position Take only positionnables modules
     *
     * @return array Modules
     */
    public function getModulesInstalled($position = 0)
    {
        return LegacyModule::getModulesInstalled((int) $position);
    }

    /**
     * Return an instance of the specified module
     *
     * @param int $moduleId Module id
     *
     * @return Module instance
     */
    public function getInstanceById($moduleId)
    {
        return LegacyModule::getInstanceById((int) $moduleId);
    }
}
