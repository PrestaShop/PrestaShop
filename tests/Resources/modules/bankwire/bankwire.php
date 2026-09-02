<?php
/**
 * 2007-2020 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class bankwire extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = [];

    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;
    /**
     * @var int
     */
    public $is_eu_compatible;

    public function __construct()
    {
        $this->name = 'bankwire';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->author = 'PrestaShop';
        $this->controllers = ['payment', 'validation'];
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Bank wire';
        $this->description = 'Accept payments for your products via bank wire transfer.';
        $this->confirmUninstall = 'Are you sure about removing these details?';
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('paymentReturn')
            || !$this->registerHook('paymentOptions')
            || !$this->registerHook('displayHome')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!Configuration::deleteByName('BANK_WIRE_DETAILS')
                || !Configuration::deleteByName('BANK_WIRE_OWNER')
                || !Configuration::deleteByName('BANK_WIRE_ADDRESS')
                || !parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * tests for ModuleGetPossibleHooks
     */
    public function hookPaymentReturn()
    {
    }

    public function hookPaymentOptions()
    {
    }

    public function hookDisplayHome()
    {
    }
}
