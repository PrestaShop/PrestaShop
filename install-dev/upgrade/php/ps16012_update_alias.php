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

function ps16012_update_alias()
{
    $step = 3000;
    $count_alias = Db::getInstance()->getValue('SELECT count(id_alias) FROM '._DB_PREFIX_.'alias');
    $nb_loop = $start = 0;

    if ($count_alias > 0) {
        $nb_loop = ceil($count_alias / $step);
    }
    for ($i = 0; $i < $nb_loop; $i++) {
        $sql = 'SELECT id_alias, alias, search FROM `'._DB_PREFIX_.'alias`';
        $start = (int) (($i+1) * $step);
        if ($aliass = Db::getInstance()->query($sql)) {
            while ($alias = Db::getInstance()->nextRow($aliass)) {
                if (is_array($alias)) {
                    Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'alias`
					SET alias = \''.pSQL(Tools::replaceAccentedChars($alias['alias'])).'\',
					search = \''.pSQL(Tools::replaceAccentedChars($alias['search'])).'\'
					WHERE id_alias = '.(int)$alias['id_alias']);
                }
            }
        }
    }

    return true;
}
