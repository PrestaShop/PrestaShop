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

class SupplyOrderStateLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Orderscustomers.Feature';

    protected $keys = array('id_supply_order_state');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('1 - Creation in progress')
                    => $this->translator->trans('1 - Creation in progress', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('2 - Order validated')
                    => $this->translator->trans('2 - Order validated', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('3 - Pending receipt')
                    => $this->translator->trans('3 - Pending receipt', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('4 - Order received in part')
                    => $this->translator->trans('4 - Order received in part', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('5 - Order received completely')
                    => $this->translator->trans('5 - Order received completely', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('6 - Order canceled')
                    => $this->translator->trans('6 - Order canceled', array(), 'Admin.Orderscustomers.Feature', $this->locale),

            ),
        );
    }
}
