<?php


class Adapter_EntityMapper {

	/**
	 * @param $id
	 * @param $id_lang
	 * @param $entity ObjectModel
	 * @param $entity_defs
	 * @param $id_shop
	 * @param $should_cache_objects
	 * @throws PrestaShopDatabaseException
	 */
	public function load($id, $id_lang, $entity, $entity_defs, $id_shop, $should_cache_objects)
	{
		// Load object from database if object id is present
		$cache_id = 'objectmodel_' . $entity_defs['classname'] . '_' . (int)$id . '_' . (int)$id_shop . '_' . (int)$id_lang;
		if (!$should_cache_objects || !Cache::isStored($cache_id)) {
			$sql = new DbQuery();
			$sql->from($entity_defs['table'], 'a');
			$sql->where('a.' . $entity_defs['primary'] . ' = ' . (int)$id);

			// Get lang informations
			if ($id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
				$sql->leftJoin($entity_defs['table'] . '_lang', 'b', 'a.' . $entity_defs['primary'] . ' = b.' . $entity_defs['primary'] . ' AND b.id_lang = ' . (int)$id_lang);
				if ($id_shop && !empty($entity_defs['multilang_shop']))
					$sql->where('b.id_shop = ' . $id_shop);
			}

			// Get shop informations
			if (Shop::isTableAssociated($entity_defs['table']))
				$sql->leftJoin($entity_defs['table'] . '_shop', 'c', 'a.' . $entity_defs['primary'] . ' = c.' . $entity_defs['primary'] . ' AND c.id_shop = ' . (int)$id_shop);
			if ($object_datas = Db::getInstance()->getRow($sql)) {
				if (!$id_lang && isset($entity_defs['multilang']) && $entity_defs['multilang']) {
					$sql = 'SELECT * FROM `' . pSQL(_DB_PREFIX_ . $entity_defs['table']) . '_lang`
								WHERE `' . bqSQL($entity_defs['primary']) . '` = ' . (int)$id
						. (($id_shop && $entity->isLangMultishop()) ? ' AND `id_shop` = ' . $id_shop : '');
					if ($object_datas_lang = Db::getInstance()->executeS($sql))
						foreach ($object_datas_lang as $row)
							foreach ($row as $key => $value) {
								if ($key != $entity_defs['primary'] && array_key_exists($key, $entity)) {
									if (!isset($object_datas[$key]) || !is_array($object_datas[$key]))
										$object_datas[$key] = array();

									$object_datas[$key][$row['id_lang']] = $value;
								}
							}
				}
				if ($should_cache_objects)
					Cache::store($cache_id, $object_datas);
			}
		} else
			$object_datas = Cache::retrieve($cache_id);

		if ($object_datas) {
			$entity->id = (int)$id;
			foreach ($object_datas as $key => $value)
				if (array_key_exists($key, $entity))
					$entity->{$key} = $value;
		}
	}

}