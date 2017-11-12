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

class StockMvtReasonLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Catalog.Feature';

    protected $keys = array('id_stock_mvt_reason');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Increase')
                    => $this->translator->trans('Increase', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Decrease')
                    => $this->translator->trans('Decrease', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Customer Order')
                    => $this->translator->trans('Customer Order', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Adjustment following an inventory of stock')
                    => $this->translator->trans('Adjustment following an inventory of stock', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Transfer to another warehouse')
                    => $this->translator->trans('Transfer to another warehouse', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Transfer from another warehouse')
                    => $this->translator->trans('Transfer from another warehouse', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Supply Order')
                    => $this->translator->trans('Supply Order', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Product Return')
                => $this->translator->trans('Product Return', array(), 'Admin.Catalog.Feature', $this->locale),

                md5('Manual Entry')
                => $this->translator->trans('Manual Entry', array(), 'Admin.Catalog.Feature', $this->locale),

            ),
        );
    }
}
