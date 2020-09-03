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

function fix_unique_specific_price()
{
    $result = Db::getInstance()->executeS('
	SELECT MIN(id_specific_price) id_specific_price
	FROM '._DB_PREFIX_.'specific_price
	GROUP BY `id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `from_quantity`, `from`, `to`');
    if (!$result || !count($result)) {
        return true;
    } // return tru if there is not any specific price in the database

    $sql = '';
    foreach ($result as $row) {
        $sql .= (int)$row['id_specific_price'].',';
    }
    $sql = rtrim($sql, ',');

    return Db::getInstance()->execute('
	DELETE FROM '._DB_PREFIX_.'specific_price
	WHERE id_specific_price NOT IN ('.$sql.')');
}
