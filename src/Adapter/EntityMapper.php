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

namespace PrestaShop\PrestaShop\Adapter;

use Cache;
use Db;
use DbQuery;
use ObjectModelCore;
use Shop;

class EntityMapper
{
    /**
     * Load ObjectModel.
     *
     * @param int $id
     * @param int $id_lang
     * @param ObjectModelCore $entity
     * @param array<string,string|array> $entity_defs
     * @param int $id_shop
     * @param bool $should_cache_objects
     *
     * @throws \PrestaShopDatabaseException
     */
    public function load($id, $id_lang, $entity, $entity_defs, $id_shop, $should_cache_objects)
    {
        // Load object from database if object id is present
        $cache_id = 'objectmodel_' . $entity_defs['classname'] . '_' . (int) $id . '_' . (int) $id_shop . '_' . (int) $id_lang;
        if (!$should_cache_objects || !Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->from($entity_defs['table'], 'a');
            $sql->where('a.`' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id);

            // Get lang informations
            if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                $sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.`' . bqSQL($entity_defs['primary']) . '` = b.`' . bqSQL($entity_defs['primary']) . '` AND b.`id_lang` = ' . (int) $id_lang);
                if ($id_shop && !empty($entity_defs['multilang_shop'])) {
                    $sql->where('b.`id_shop` = ' . (int) $id_shop);
                }
            }

            // Get shop informations
            if (Shop::isTableAssociated($entity_defs['table'])) {
                $sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.`' . bqSQL($entity_defs['primary']) . '` = c.`' . bqSQL($entity_defs['primary']) . '` AND c.`id_shop` = ' . (int) $id_shop);
            }

            if ($object_datas = Db::getInstance()->getRow($sql)) {
                $objectVars = get_object_vars($entity);
                if (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
                    $sql = 'SELECT *
							FROM `' . bqSQL(_DB_PREFIX_ . $entity_defs['table']) . '_lang`
							WHERE `' . bqSQL($entity_defs['primary']) . '` = ' . (int) $id
                            . (($id_shop && $entity->isLangMultishop()) ? ' AND `id_shop` = ' . (int) $id_shop : '');

                    if ($object_datas_lang = Db::getInstance()->executeS($sql)) {
                        foreach ($object_datas_lang as $row) {
                            foreach ($row as $key => $value) {
                                if ($key != $entity_defs['primary'] && array_key_exists($key, $objectVars)) {
                                    if (!isset($object_datas[$key]) || !is_array($object_datas[$key])) {
                                        $object_datas[$key] = [];
                                    }

                                    $object_datas[$key][$row['id_lang']] = $value;
                                }
                            }
                        }
                    }
                }

                $entity->id = (int) $id;
                foreach ($object_datas as $key => $value) {
                    if (array_key_exists($key, $entity_defs['fields'])
                        || array_key_exists($key, $objectVars)) {
                        if (in_array($entity_defs['fields'][$key]['type'], [
                            \ObjectModel::TYPE_INT,
                            \ObjectModel::TYPE_BOOL,
                            \ObjectModel::TYPE_FLOAT,
                        ])) {
                            $entity->{$key} = (string) $value;
                        } else {
                            $entity->{$key} = $value;
                        }
                    } else {
                        unset($object_datas[$key]);
                    }
                }
                if ($should_cache_objects) {
                    Cache::store($cache_id, $object_datas);
                }
            }
        } else {
            $object_datas = Cache::retrieve($cache_id);
            if ($object_datas) {
                $entity->id = (int) $id;
                foreach ($object_datas as $key => $value) {
                    $entity->{$key} = $value;
                }
            }
        }
    }
}
