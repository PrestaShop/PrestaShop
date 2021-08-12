<?php
/**
 * 2007-2018 PrestaShop
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
if (!defined('_PS_VERSION_')) {
    exit;
}

class ganalytics extends Module
{
    /*
    * This module will return false on install !!!
    *
    */
    public function __construct()
    {
        $this->name = 'ganalytics';
        $this->tab = 'analytics_stats';
        $this->version = '2.3.4';
        $this->author = 'PrestaShop';
        $this->module_key = 'fd2aaefea84ac1bb512e6f1878d990b8';
        $this->bootstrap = true;

        $this->displayName = 'Google Analytics';
        $this->description = 'Gain clear insights into important metrics about your customers, using Google Analytics';
        $this->confirmUninstall = 'Are you sure you want to uninstall Google Analytics? You will lose all the data related to this module.';
    }

    public function install()
    {
        return false;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }
}
