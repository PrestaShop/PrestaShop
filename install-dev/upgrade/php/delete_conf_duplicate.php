<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function delete_conf_duplicate()
{
    $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'configuration`
                WHERE `name`=\'PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE\' AND `date_upd` IN (
                    SELECT
                        MAX(`date_upd`)
                    FROM
                        `'._DB_PREFIX_.'configuration`
                    WHERE
                        `name`=\'PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE\'
                    GROUP BY
                        `id_shop_group`,
                        `id_shop`
                )
                GROUP BY `date_upd`
    ';
    $correctRows = Db::getInstance()->executeS($sql);

    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE name=\'PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE\'');

    foreach ($correctRows as $row) {
        Db::getInstance()->execute("
          INSERT INTO `"._DB_PREFIX_."configuration` (`id_configuration`, `id_shop_group`, `id_shop`, `name`, `value`, `date_add`, `date_upd`)
            VALUES (
            '".$row['id_configuration']."',
            '".$row['id_shop_group']."',
            '".$row['id_shop']."',
            '".$row['name']."',
            '".$row['value']."',
            '".$row['date_add']."',
            '".$row['date_upd']."'
            )
        ");
    }
}
