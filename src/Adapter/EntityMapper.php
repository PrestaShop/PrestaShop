<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Cache;
use Db;
use DbQuery;
use Language;
use ObjectModel;
use ObjectModelCore;
use PrestaShopDatabaseException;
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
     * @throws PrestaShopDatabaseException
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
                    } else {
                        // Initialize multilingual fields with empty values for each language.
                        // This prevents errors when _lang records are missing (e.g. entity deleted from a shop but accessed in "all shops" context).
                        $languages = Language::getLanguages();
                        foreach ($entity_defs['fields'] as $key => $field) {
                            if (!empty($field['lang'])) {
                                foreach ($languages as $language) {
                                    $object_datas[$key][$language['id_lang']] = '';
                                }
                            }
                        }
                    }
                }

                $entity->id = (int) $id;
                foreach ($object_datas as $key => $value) {
                    if (array_key_exists($key, $entity_defs['fields'])
                        || array_key_exists($key, $objectVars)) {
                        if (isset($entity_defs['fields'][$key]['type']) && in_array($entity_defs['fields'][$key]['type'], [
                            ObjectModel::TYPE_BOOL,
                        ])) {
                            if (is_array($value)) {
                                array_walk($value, function (&$v) { $v = strval($v); });
                                $entity->{$key} = $value;
                            } else {
                                $entity->{$key} = strval($value);
                            }
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

    /**
     * Warm the ObjectModel cache for a whole set of ids in two batched queries instead of the two
     * queries load() runs per object (issue #14979). Each entry is stored under the exact same
     * cache_id and with the exact same payload load() would produce, so a subsequent
     * `new <Entity>($id, $id_lang, $id_shop)` is served from cache via load()'s cache-hit branch
     * with no behavioural change. A no-op when object caching is off, or for ids already cached, or
     * for ids whose base row is absent (left for load() to resolve exactly as today).
     *
     * @param int[] $ids
     * @param int $id_lang
     * @param ObjectModelCore $entity a sample entity of the target class (no id required)
     * @param array<string,string|array> $entity_defs
     * @param int $id_shop
     * @param bool $should_cache_objects
     *
     * @throws PrestaShopDatabaseException
     */
    public function prefetch(array $ids, $id_lang, $entity, $entity_defs, $id_shop, $should_cache_objects)
    {
        if (!$should_cache_objects) {
            return;
        }
        $id_lang = (int) $id_lang;
        $id_shop = (int) $id_shop;
        $primary = $entity_defs['primary'];

        $missing = [];
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id && !Cache::isStored('objectmodel_' . $entity_defs['classname'] . '_' . $id . '_' . $id_shop . '_' . $id_lang)) {
                $missing[] = $id;
            }
        }
        if (empty($missing)) {
            return;
        }

        // Same base query load() builds, with IN() instead of a single id.
        $sql = new DbQuery();
        $sql->from($entity_defs['table'], 'a');
        $sql->where('a.`' . bqSQL($primary) . '` IN (' . implode(',', $missing) . ')');
        if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
            $sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.`' . bqSQL($primary) . '` = b.`' . bqSQL($primary) . '` AND b.`id_lang` = ' . $id_lang);
            if ($id_shop && !empty($entity_defs['multilang_shop'])) {
                $sql->where('b.`id_shop` = ' . $id_shop);
            }
        }
        if (Shop::isTableAssociated($entity_defs['table'])) {
            $sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.`' . bqSQL($primary) . '` = c.`' . bqSQL($primary) . '` AND c.`id_shop` = ' . $id_shop);
        }
        $base_rows = Db::getInstance()->executeS($sql);
        if (!is_array($base_rows)) {
            return;
        }
        $base_by_id = [];
        foreach ($base_rows as $row) {
            $base_by_id[(int) $row[$primary]] = $row;
        }

        // Same all-languages query load() runs when no language is requested.
        $lang_by_id = [];
        if (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
            $lang_sql = 'SELECT * FROM `' . bqSQL(_DB_PREFIX_ . $entity_defs['table']) . '_lang`
                WHERE `' . bqSQL($primary) . '` IN (' . implode(',', $missing) . ')'
                . (($id_shop && $entity->isLangMultishop()) ? ' AND `id_shop` = ' . $id_shop : '');
            $lang_rows = Db::getInstance()->executeS($lang_sql);
            if (is_array($lang_rows)) {
                foreach ($lang_rows as $row) {
                    $lang_by_id[(int) $row[$primary]][] = $row;
                }
            }
        }

        // In the all-languages path, load() initialises every multilang field to ''
        // for each language when an entity has no _lang rows; mirror that here so the
        // primed object is identical to what load() would produce.
        $allLanguages = (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang'])
            ? Language::getLanguages()
            : [];

        $objectVars = get_object_vars($entity);
        foreach ($missing as $id) {
            if (!isset($base_by_id[$id])) {
                // load() would get false here and cache nothing; leave it untouched.
                continue;
            }
            $object_datas = $base_by_id[$id];

            if (isset($lang_by_id[$id])) {
                foreach ($lang_by_id[$id] as $row) {
                    foreach ($row as $key => $value) {
                        if ($key != $primary && array_key_exists($key, $objectVars)) {
                            if (!isset($object_datas[$key]) || !is_array($object_datas[$key])) {
                                $object_datas[$key] = [];
                            }
                            $object_datas[$key][$row['id_lang']] = $value;
                        }
                    }
                }
            } elseif (!empty($allLanguages)) {
                // No _lang rows for this entity: replicate load()'s empty-init else branch.
                foreach ($entity_defs['fields'] as $key => $field) {
                    if (!empty($field['lang'])) {
                        foreach ($allLanguages as $language) {
                            $object_datas[$key][$language['id_lang']] = '';
                        }
                    }
                }
            }

            // Same field filter load() applies before Cache::store().
            foreach ($object_datas as $key => $value) {
                if (!array_key_exists($key, $entity_defs['fields']) && !array_key_exists($key, $objectVars)) {
                    unset($object_datas[$key]);
                }
            }

            Cache::store('objectmodel_' . $entity_defs['classname'] . '_' . $id . '_' . $id_shop . '_' . $id_lang, $object_datas);
        }
    }
}
