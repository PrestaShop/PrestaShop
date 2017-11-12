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

class OrderStateLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Orderscustomers.Feature';

    protected $keys = array('id_order_state');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Awaiting check payment')
                    => $this->translator->trans('Awaiting check payment', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Payment accepted')
                    => $this->translator->trans('Payment accepted', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Processing in progress')
                    => $this->translator->trans('Processing in progress', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Shipped')
                    => $this->translator->trans('Shipped', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Delivered')
                    => $this->translator->trans('Delivered', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Canceled')
                    => $this->translator->trans('Canceled', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Refunded')
                    => $this->translator->trans('Refunded', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Payment error')
                    => $this->translator->trans('Payment error', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('On backorder (paid)')
                    => $this->translator->trans('On backorder (paid)', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('On backorder (not paid)')
                    => $this->translator->trans('On backorder (not paid)', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Awaiting bank wire payment')
                    => $this->translator->trans('Awaiting bank wire payment', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Remote payment accepted')
                    => $this->translator->trans('Remote payment accepted', array(), 'Admin.Orderscustomers.Feature', $this->locale),

                md5('Awaiting Cash On Delivery validation')
                    => $this->translator->trans('Awaiting Cash On Delivery validation', array(), 'Admin.Orderscustomers.Feature', $this->locale),

            ),
        );
    }
}
