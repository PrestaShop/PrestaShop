<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}


class Ps_Emailsubscription extends Module
{
    public function __construct()
    {
        $this->name = 'ps_emailsubscription';
        $this->need_instance = 0;

        $this->controllers = array('verification');

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('E-mail subscription form');
        $this->description = $this->l('Adds a form for newsletter subscription.');
        $this->confirmUninstall = $this->l('Are you sure that you want to delete all of your contacts?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->error = false;
        $this->valid = false;
        $this->_files = array(
            'name' => array('newsletter_conf', 'newsletter_voucher'),
            'ext' => array(
                0 => 'html',
                1 => 'txt'
            )
        );
    }

    public function install()
    {
        if (!parent::install() || !Configuration::updateValue('PS_NEWSLETTER_RAND', rand().rand()) || !$this->registerHook(array('displayFooterBefore', 'actionCustomerAccountAdd'))) {
            return false;
        }
        return true;
    }
}
