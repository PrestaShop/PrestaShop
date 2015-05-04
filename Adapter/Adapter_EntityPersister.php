<?php

class Adapter_EntityPersister
{
    public function save($entity, $null_values = false, $auto_date = true)
	{
        if ((int)$entity->id > 0) {
            return $entity->update($null_values, $null_values);
        } else {
            return $entity->add($auto_date, $null_values);
        }
	}

    public function add($entity, $auto_date = true, $null_values = false)
	{
		if (isset($entity->id) && !$entity->force_id)
			unset($entity->id);

		// @hook actionObject*AddBefore
		Hook::exec('actionObjectAddBefore', array('object' => $entity));
		Hook::exec('actionObject'.get_class($entity).'AddBefore', array('object' => $entity));

		// Automatically fill dates
		if ($auto_date && property_exists($entity, 'date_add'))
			$entity->date_add = date('Y-m-d H:i:s');
		if ($auto_date && property_exists($entity, 'date_upd'))
			$entity->date_upd = date('Y-m-d H:i:s');

		if (Shop::isTableAssociated($entity->def['table']))
		{
			$id_shop_list = Shop::getContextListShopID();
			if (count($entity->id_shop_list) > 0)
				$id_shop_list = $entity->id_shop_list;
		}
		// Database insertion
		if (Shop::checkIdShopDefault($entity->def['table']))
			$entity->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
		if (!$result = Db::getInstance()->insert($entity->def['table'], $entity->getFields(), $null_values))
			return false;
		// Get object id in database
		$entity->id = Db::getInstance()->Insert_ID();

		// Database insertion for multishop fields related to the object
		if (Shop::isTableAssociated($entity->def['table']))
		{
			$fields = $entity->getFieldsShop();
			$fields[$entity->def['primary']] = (int)$entity->id;

			foreach ($id_shop_list as $id_shop)
			{
				$fields['id_shop'] = (int)$id_shop;
				$result &= Db::getInstance()->insert($entity->def['table'].'_shop', $fields, $null_values);
			}
		}

		if (!$result)
			return false;

		// Database insertion for multilingual fields related to the object
		if (!empty($entity->def['multilang']))
		{
			$fields = $entity->getFieldsLang();
			if ($fields && is_array($fields))
			{
				$shops = Shop::getCompleteListOfShopsID();
				$asso = Shop::getAssoTable($entity->def['table'].'_lang');
				foreach ($fields as $field)
				{
					foreach (array_keys($field) as $key)
						if (!Validate::isTableOrIdentifier($key))
							throw new PrestaShopException('key '.$key.' is not table or identifier');
					$field[$entity->def['primary']] = (int)$entity->id;

					if ($asso !== false && $asso['type'] == 'fk_shop')
					{
						foreach ($shops as $id_shop)
						{
							$field['id_shop'] = (int)$id_shop;
							$result &= Db::getInstance()->insert($entity->def['table'].'_lang', $field);
						}
					}
					else
						$result &= Db::getInstance()->insert($entity->def['table'].'_lang', $field);
				}
			}
		}

		// @hook actionObject*AddAfter
		Hook::exec('actionObjectAddAfter', array('object' => $entity));
		Hook::exec('actionObject'.get_class($entity).'AddAfter', array('object' => $entity));

		return $result;
	}

    public function update($entity, $null_values = false)
	{
		// @hook actionObject*UpdateBefore
		Hook::exec('actionObjectUpdateBefore', array('object' => $entity));
		Hook::exec('actionObject'.get_class($entity).'UpdateBefore', array('object' => $entity));

		$entity->clearCache();

		// Automatically fill dates
		if (array_key_exists('date_upd', $entity))
		{
			$entity->date_upd = date('Y-m-d H:i:s');
			if (isset($entity->update_fields) && is_array($entity->update_fields) && count($entity->update_fields))
				$entity->update_fields['date_upd'] = true;
		}

		// Automatically fill dates
		if (array_key_exists('date_add', $entity) && $entity->date_add == null)
		{
			$entity->date_add = date('Y-m-d H:i:s');
			if (isset($entity->update_fields) && is_array($entity->update_fields) && count($entity->update_fields))
				$entity->update_fields['date_add'] = true;
		}

		$id_shop_list = Shop::getContextListShopID();
		if (count($entity->id_shop_list) > 0)
			$id_shop_list = $entity->id_shop_list;

		if (Shop::checkIdShopDefault($entity->def['table']) && !$entity->id_shop_default)
			$entity->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
		// Database update
		if (!$result = Db::getInstance()->update($entity->def['table'], $entity->getFields(), '`'.pSQL($entity->def['primary']).'` = '.(int)$entity->id, 0, $null_values))
			return false;

		// Database insertion for multishop fields related to the object
		if (Shop::isTableAssociated($entity->def['table']))
		{
			$fields = $entity->getFieldsShop();
			$fields[$entity->def['primary']] = (int)$entity->id;
			if (is_array($entity->update_fields))
			{
				$update_fields = $entity->update_fields;
				$entity->update_fields = null;
				$all_fields = $entity->getFieldsShop();
				$all_fields[$entity->def['primary']] = (int)$entity->id;
				$entity->update_fields = $update_fields;
			}
			else
				$all_fields = $fields;

			foreach ($id_shop_list as $id_shop)
			{
				$fields['id_shop'] = (int)$id_shop;
				$all_fields['id_shop'] = (int)$id_shop;
				$where = $entity->def['primary'].' = '.(int)$entity->id.' AND id_shop = '.(int)$id_shop;

				// A little explanation of what we do here : we want to create multishop entry when update is called, but
				// only if we are in a shop context (if we are in all context, we just want to update entries that alread exists)
				$shop_exists = Db::getInstance()->getValue('SELECT '.$entity->def['primary'].' FROM '._DB_PREFIX_.$entity->def['table'].'_shop WHERE '.$where);
				if ($shop_exists)
					$result &= Db::getInstance()->update($entity->def['table'].'_shop', $fields, $where, 0, $null_values);
				elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
					$result &= Db::getInstance()->insert($entity->def['table'].'_shop', $all_fields, $null_values);
			}
		}

		// Database update for multilingual fields related to the object
		if (isset($entity->def['multilang']) && $entity->def['multilang'])
		{
			$fields = $entity->getFieldsLang();
			if (is_array($fields))
			{
				foreach ($fields as $field)
				{
					foreach (array_keys($field) as $key)
						if (!Validate::isTableOrIdentifier($key))
							throw new PrestaShopException('key '.$key.' is not a valid table or identifier');

					// If this table is linked to multishop system, update / insert for all shops from context
					if ($entity->isLangMultishop())
					{
						$id_shop_list = Shop::getContextListShopID();
						if (count($entity->id_shop_list) > 0)
							$id_shop_list = $entity->id_shop_list;
						foreach ($id_shop_list as $id_shop)
						{
							$field['id_shop'] = (int)$id_shop;
							$where = pSQL($entity->def['primary']).' = '.(int)$entity->id
										.' AND id_lang = '.(int)$field['id_lang']
										.' AND id_shop = '.(int)$id_shop;

							if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$entity->def['table']).'_lang WHERE '.$where))
								$result &= Db::getInstance()->update($entity->def['table'].'_lang', $field, $where);
							else
								$result &= Db::getInstance()->insert($entity->def['table'].'_lang', $field);
						}
					}
					// If this table is not linked to multishop system ...
					else
					{
						$where = pSQL($entity->def['primary']).' = '.(int)$entity->id
									.' AND id_lang = '.(int)$field['id_lang'];
						if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$entity->def['table']).'_lang WHERE '.$where))
							$result &= Db::getInstance()->update($entity->def['table'].'_lang', $field, $where);
						else
							$result &= Db::getInstance()->insert($entity->def['table'].'_lang', $field, $null_values);
					}
				}
			}
		}

		// @hook actionObject*UpdateAfter
		Hook::exec('actionObjectUpdateAfter', array('object' => $entity));
		Hook::exec('actionObject'.get_class($entity).'UpdateAfter', array('object' => $entity));

		return $result;
	}
}
