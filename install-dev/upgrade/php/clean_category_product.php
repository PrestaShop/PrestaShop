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

/* Remove duplicate entries from ps_category_product */
function clean_category_product()
{
    $list = Db::getInstance()->executeS('
	SELECT id_category, id_product, COUNT(*) n
	FROM '._DB_PREFIX_.'category_product
	GROUP BY CONCAT(id_category,\'|\',id_product)
	HAVING n > 1');

    $result = true;
    if ($list) {
        foreach ($list as $l) {
            $result &= Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'category_product
			WHERE id_product = '.(int)$l['id_product'].' AND id_category = '.(int)$l['id_category'].' LIMIT '.(int)($l['n'] - 1));
        }
    }

    return $result;
}
