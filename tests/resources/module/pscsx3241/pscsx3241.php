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

class pscsx3241 extends Module
{
    public function __construct()
    {
        $this->name = 'pscsx3241';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
        $this->author = 'PSCSX-3241';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Module PSCSX-3241');
        $this->description = $this->l('A module to test bug PSCSX-3241');
    }

    public function install()
    {
        if (parent::install() == false) {
            return false;
        }
        return true;
    }
}
