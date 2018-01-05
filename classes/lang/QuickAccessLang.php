<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class QuickAccessLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Navigation.Header';

    protected $keys = array('id_quick_access');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Home') => $this->translator->trans('Home', array(), 'Admin.Navigation.Header', $this->locale),
                md5('My Shop') => $this->translator->trans('My Shop', array(), 'Admin.Navigation.Header', $this->locale),
                md5('New category') => $this->translator->trans('New category', array(), 'Admin.Navigation.Header', $this->locale),
                md5('New product') => $this->translator->trans('New product', array(), 'Admin.Navigation.Header', $this->locale),
                md5('New voucher') => $this->translator->trans('New voucher', array(), 'Admin.Navigation.Header', $this->locale),
                md5('Orders') => $this->translator->trans('Orders', array(), 'Admin.Navigation.Header', $this->locale),
                md5('Installed modules') => $this->translator->trans('Installed modules', array(), 'Admin.Navigation.Header', $this->locale),
            ),
        );
    }
}
