<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

use ProductSupplier;
use Supplier;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This class will provide data from DB / ORM about Supplier.
 */
class SupplierDataProvider
{
    /**
     * Get all suppliers.
     *
     * @param bool $get_nb_products
     * @param int $id_lang
     * @param bool $active
     * @param bool $p
     * @param bool $n
     * @param bool $all_groups
     *
     * @return array Suppliers
     */
    public function getSuppliers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_groups = false)
    {
        return Supplier::getSuppliers($get_nb_products, $id_lang, $active, $p, $n, $all_groups);
    }

    /**
     * Get product suppliers.
     *
     * @param int $id_product
     * @param bool $group_by_supplier
     *
     * @return array Suppliers
     */
    public function getProductSuppliers($id_product, $group_by_supplier = true)
    {
        $suppliersCollection = ProductSupplier::getSupplierCollection($id_product, $group_by_supplier);

        return $suppliersCollection->getResults();
    }

    /**
     * For a given product and supplier, gets the product supplier data.
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_supplier
     *
     * @return array
     */
    public function getProductSupplierData($id_product, $id_product_attribute, $id_supplier)
    {
        return ProductSupplier::getProductSupplierData($id_product, $id_product_attribute, $id_supplier);
    }

    /**
     * Get supplier name by id.
     *
     * @param int $id_supplier
     *
     * @return string
     */
    public function getNameById($id_supplier)
    {
        return Supplier::getNameById($id_supplier);
    }
}
