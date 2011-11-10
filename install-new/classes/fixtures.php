<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class InstallFixtures
{
	/**
	 * @var array List of errors logged during fixtures process
	 */
	private $errors = array();

	/**
	 * @var InstallLanguages
	 */
	protected $language;

	/**
	 * @var Db
	 */
	protected $db;

	/**
	 * @var array List of installed languages
	 */
	protected $installed_languages = array();

	/**
	 * @var array Store created ids for each object with ID argument
	 */
	protected $ids = array();

	/**
	 * Process fixtures installation
	 */
	abstract protected function install();

	public function __construct(InstallLanguages $language)
	{
		require_once _PS_ROOT_DIR_.'/images.inc.php';

		$this->db = Db::getInstance();
		$this->language = $language;
		Db::getInstance()->delete('prefix_manufacturer');
		Db::getInstance()->delete('prefix_manufacturer_lang');
		Db::getInstance()->delete('prefix_supplier');
		Db::getInstance()->delete('prefix_supplier_lang');
		Db::getInstance()->delete('prefix_address');
		Db::getInstance()->delete('prefix_product');
		Db::getInstance()->delete('prefix_product_lang');
		Db::getInstance()->delete('prefix_category', 'id_category <> 1');
		Db::getInstance()->delete('prefix_category_product');
		Db::getInstance()->delete('prefix_category_lang', 'id_category <> 1');
		Db::getInstance()->delete('prefix_scene');
		Db::getInstance()->delete('prefix_scene_lang');
		Db::getInstance()->delete('prefix_scene_products');
		Db::getInstance()->delete('prefix_scene_category');
		Db::getInstance()->delete('prefix_attribute_group');
		Db::getInstance()->delete('prefix_attribute_group_lang');
		Db::getInstance()->delete('prefix_attribute');
		Db::getInstance()->delete('prefix_attribute_lang');
		Db::getInstance()->delete('prefix_product_attribute');
		Db::getInstance()->delete('prefix_product_attribute_combination');
		Db::getInstance()->delete('prefix_product_attribute_image');
		Db::getInstance()->delete('prefix_order_message');
		Db::getInstance()->delete('prefix_order_message_lang');
		Db::getInstance()->delete('prefix_feature');
		Db::getInstance()->delete('prefix_feature_lang');
		Db::getInstance()->delete('prefix_feature_value');
		Db::getInstance()->delete('prefix_feature_value_lang');
		Db::getInstance()->delete('prefix_feature_product');
		Db::getInstance()->delete('prefix_store');
		Db::getInstance()->delete('prefix_image');
		Db::getInstance()->delete('prefix_image_lang');
		Db::getInstance()->delete('prefix_tag');
		Db::getInstance()->delete('prefix_alias');
		Db::getInstance()->delete('prefix_customer');
		Db::getInstance()->delete('prefix_guest');
		Db::getInstance()->delete('prefix_connections');
		Db::getInstance()->delete('prefix_customer_group');
		Db::getInstance()->delete('prefix_cart');
		Db::getInstance()->delete('prefix_cart_product');
		Db::getInstance()->delete('prefix_orders');
		Db::getInstance()->delete('prefix_order_detail');
		Db::getInstance()->delete('prefix_order_history');
		Db::getInstance()->delete('prefix_range_price');
		Db::getInstance()->delete('prefix_range_weight');
		Db::getInstance()->delete('prefix_delivery');
		Db::getInstance()->delete('prefix_specific_price');
		foreach (Language::getLanguages(false) as $lang)
			$this->installed_languages[$lang['id_lang']] = $lang['iso_code'];
	}

	/**
	 * Log an error during fixtures process
	 */
	protected function setError()
	{
	}

	/**
	 * Get list of errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Install fixtures
	 */
	public function process()
	{
		$this->loadData();
		$this->install();

		Search::indexation(true);
	}

	protected function getId($entity, $identifier)
	{
		return isset($this->ids[$entity.':'.$identifier]) ? $this->ids[$entity.':'.$identifier] : 0;
	}

	protected function hydrateEntity(ObjectModel $object, array $data, array $mapper)
	{
		$hydrate_data = $this->getHydratedData($data, $mapper);
		$object->hydrate($hydrate_data);
	}

	protected function getHydratedData(array $data, array $mapper)
	{
		$hydrate_data = array();
		foreach ($mapper as $key => $info)
		{
			$value = (array_key_exists($key, $data)) ? $data[$key] : $info['default'];
			switch ($info['type'])
			{
				case 'bool':
					if (strtolower($value) == 'true')
						$value = true;
					else if (strtolower($value) == 'false')
						$value = false;
					$hydrate_data[$key] = (bool)$value;
				break;

				case 'int':
					$hydrate_data[$key] = (int)$value;
				break;

				case 'float':
					$hydrate_data[$key] = (float)$value;
				break;

				case 'string':
					$hydrate_data[$key] = (string)$value;
				break;

				case 'date':
					if (!Validate::isDateFormat($value))
						$value = '0000-00-00';
					$hydrate_data[$key] = $value;
				break;

				case 'relation':
					if (!isset($data[$info['field']]) && isset($data[$key]))
						$hydrate_data[$info['field']] = $this->getId($info['entity'], $value);
				break;

				case 'relation_table':
					if (!isset($data[$info['field']]) && isset($data[$key]))
						$hydrate_data[$info['field']] = $this->db->getValue('
							SELECT '.$info['field'].'
							FROM '._DB_PREFIX_.$info['table'].'
							WHERE '.$info['target_field'].' = \''.pSQL($data[$key]).'\'
						');
			}
		}

		return $hydrate_data;
	}

	protected function fillTranslation($entity, $id, $tag)
	{
		$translations = array();
		foreach ($this->installed_languages as $id_lang => $iso)
			$translations[$id_lang] = str_replace(array('\n', '\r'), array("\n", "\r"), $this->language->getFixtureTranslation($iso, $entity.'_'.$id.'_'.$tag));
		return $translations;
	}

	protected function createEntityImages($entity, $id, $entity_id, $image_type, $target_folder)
	{
		return;
		$path = _PS_INSTALL_FIXTURES_PATH_.'apple/img/'.$entity.'/';
		$dst_path =  _PS_INSTALL_FIXTURES_PATH_.'apple/img/TESTS/'.$target_folder.'/';
		if (!@copy($path.$id.'.jpg', $dst_path.$entity_id.'.jpg'))
		{
			$this->setError($this->language->l('Cannot create image "%s"', $id));
			return;
		}
		@chmod($dst_path.$entity_id.'.jpg', 0644);

		$types = ImageType::getImagesTypes($image_type);
		foreach ($types as $type)
		{
			$subpath = '';
			if ($type['name'] == 'thumb_scene')
				$subpath = 'thumbs/';

			$origin_file = $path.$subpath.$id.'-'.$type['name'].'.jpg';
			$target_file = $dst_path.$subpath.$entity_id.'-'.$type['name'].'.jpg';

			// Test if dest folder is writable
			if (!is_writable(dirname($target_file)))
				$this->setError($this->language->l('Cannot create image "%1$s" (bad permissions on folder "%2$s")', $id.'-'.$type['name'], dirname($target_file)));
			// If a file named folder/entity-type.jpg exists just copy it, this is an optimisation in order to prevent to much resize
			else if (file_exists($origin_file))
			{
				if (!@copy($origin_file, $target_file))
					$this->setError($this->language->l('Cannot create image "%s"', $id.'-'.$type['name']));
				@chmod($target_file, 0644);
			}
			// Resize the image if no cache was prepared in fixtures
			else if (!imageResize($path.$id.'.jpg', $target_file, $type['width'], $type['height']))
				$this->setError($this->language->l('Cannot create image "%s"', $id.'-'.$type['name']));
		}
	}

	protected function createProductImages(Image $image, $id, $id_product)
	{
		return;
		$path = _PS_INSTALL_FIXTURES_PATH_.'apple/img/product/';
		$dst_path = $image->getPathForCreation();
		//$dst_path = str_replace(_PS_ROOT_DIR_.'/img/', _PS_INSTALL_FIXTURES_PATH_.'apple/img/TESTS/', $dst_path);
		if (!@copy($path.$id.'.jpg', $dst_path.'.'.$image->image_format))
		{
			$this->setError($this->language->l('Cannot create image "%s"', $id));
			return;
		}
		@chmod($dst_path.'.'.$image->image_format, 0644);

		$types = ImageType::getImagesTypes('products');
		foreach ($types as $type)
		{
			$origin_file = $path.$id.'-'.$type['name'].'.jpg';
			$target_file = $dst_path.'-'.$type['name'].'.'.$image->image_format;

			// Test if dest folder is writable
			if (!is_writable(dirname($target_file)))
				$this->setError($this->language->l('Cannot create image "%1$s" (bad permissions on folder "%2$s")', $id.'-'.$type['name'], dirname($target_file)));
			// If a file named folder/entity-type.jpg exists just copy it, this is an optimisation in order to prevent to much resize
			else if (file_exists($origin_file))
			{
				if (!@copy($origin_file, $target_file))
					$this->setError($this->language->l('Cannot create image "%s"', $id.'-'.$type['name']));
				@chmod($target_file, 0644);
			}
			// Resize the image if no cache was prepared in fixtures
			else if (!imageResize($path.$id.'.jpg', $target_file, $type['width'], $type['height']))
				$this->setError($this->language->l('Cannot create image "%s"', $id.'-'.$type['name']));
		}
	}

	protected function loadData()
	{
		$this->loadEntity('manufacturer');
		$this->loadEntity('supplier');
		$this->loadEntity('alias');
		$this->loadEntity('ordermessage');
		$this->loadEntity('carrier');
		$this->loadEntity('range');
		$this->loadEntity('delivery');
		$this->loadEntity('customer');
		$this->loadEntity('guest');
		$this->loadEntity('address');
		$this->loadEntity('store');
		$this->loadEntity('category');
		$this->loadEntity('feature');
		$this->loadEntity('featurevalue');
		$this->loadEntity('attributegroup');
		$this->loadEntity('attribute');
		$this->loadEntity('product');
		$this->loadEntity('specificprice');
		$this->loadEntity('image');
		$this->loadEntity('productattribute');
		$this->loadEntity('scene');
		$this->loadEntity('cart');
		$this->loadEntity('order');
	}

	protected function loadEntity($entity)
	{
		$entity_file = _PS_INSTALL_FIXTURES_PATH_.'apple/data/'.$entity.'.xml';
		if (!file_exists($entity_file))
			return;

		$xml = simplexml_load_file($entity_file);
		foreach ($xml->$entity as $node)
		{
			// Entity identifier
			$identifier = (string)$node['id'];

			// Entity data
			$data = array();
			foreach ($node->attributes() as $attr_name => $attr_value)
				$data[$attr_name] = (string)$attr_value;

			foreach ($node->children() as $child_node)
				$data[$child_node->getName()] = $this->parseNode($child_node);

			$method = 'Install'.ucfirst($entity);
			$this->$method($identifier, $data);
		}
	}

	protected function parseNode(SimpleXMLElement $node)
	{
		$children = $node->children();
		if (!$children)
			return (string)$node;
		else
		{
			$data = array();
			foreach ($node->attributes() as $k => $v)
				$data[$k] = (string)$v;

			foreach ($children as $child)
				if ($child->getName() == 'item')
					$data[] = $this->parseNode($child);
				else
					$data[$child->getName()] = $this->parseNode($child);
			return $data;
		}
	}

	protected function installGenericEntity($classname, $identifier, array $data, array $mapper, array $lang_fields = array())
	{
		$entity = strtolower($classname);
		$object = new $classname();
		$this->hydrateEntity($object, $data, $mapper);

		foreach ($lang_fields as $field)
			$object->$field = $this->fillTranslation($entity, $identifier, $field);

		if (!$object->add())
		{
			$this->setError($this->language->l('Cannot create entity "%s" with identifier "%s"', $entity, $identifier));
			return false;
		}

		$this->ids[$entity.':'.$identifier] = $object->id;
		return $object;
	}

	protected function installManufacturer($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Manufacturer',
			$identifier,
			$data,
			array(
				'name' =>	array('type' => 'string', 'default' => ''),
				'active' =>	array('type' => 'bool', 'default' => true),
			),
			array('description', 'short_description', 'meta_description', 'meta_title', 'meta_keywords')
		);

		if ($object)
			$this->createEntityImages('manufacturer', $identifier, $object->id, 'manufacturers', 'm');
	}

	protected function installSupplier($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Supplier',
			$identifier,
			$data,
			array(
				'name' =>	array('type' => 'string', 'default' => ''),
				'active' =>	array('type' => 'bool', 'default' => true),
			),
			array('description', 'short_description', 'meta_description', 'meta_title', 'meta_keywords')
		);

		if ($object)
			$this->createEntityImages('supplier', $identifier, $object->id, 'suppliers', 'su');
	}

	protected function installAlias($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Alias',
			$identifier,
			$data,
			array(
				'alias' =>	array('type' => 'string', 'default' => ''),
				'search' =>	array('type' => 'string', 'default' => ''),
				'active' =>	array('type' => 'bool', 'default' => true),
			)
		);
	}

	protected function installOrderMessage($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'OrderMessage',
			$identifier,
			$data,
			array(),
			array('name', 'message')
		);
	}

	protected function installCarrier($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Carrier',
			$identifier,
			$data,
			array(
				'id_tax_rules_group' =>	array('type' => 'int', 'default' => 0),
				'name' =>				array('type' => 'string', 'default' => ''),
				'shipping_handling' =>	array('type' => 'bool', 'default' => false),
				'is_free' =>			array('type' => 'bool', 'default' => false),
				'active' =>				array('type' => 'bool', 'default' => true),
			),
			array('delay')
		);

		if ($object)
		{
			// Add zones to carrier
			if (isset($data['zones']))
			{
				foreach ($data['zones'] as $zone)
					$object->addZone(Zone::getIdByName($zone));
			}

			// Add carrier to groups
			if (isset($data['groups']))
			{
				foreach ($data['groups'] as $id_group)
					$this->db->autoExecute(_DB_PREFIX_.'carrier_group', array(
						'id_carrier' =>	(int)$object->id,
						'id_group' =>	(int)$id_group,
					), 'INSERT');
			}
		}
	}

	protected function installCustomer($identifier, array $data)
	{
		if (isset($data['passwd']))
			$data['passwd'] = Tools::encrypt($data['passwd']);

		$object = $this->installGenericEntity(
			'Customer',
			$identifier,
			$data,
			array(
				'firstname' =>			array('type' => 'string', 'default' => ''),
				'lastname' =>			array('type' => 'string', 'default' => ''),
				'email' =>				array('type' => 'string', 'default' => ''),
				'passwd' =>				array('type' => 'string', 'default' => ''),
				'birthday' =>			array('type' => 'date', 'default' => '0000-00-00'),
				'id_gender' =>			array('type' => 'int', 'default' => 1),
				'id_default_group' =>	array('type' => 'int', 'default' => 1),
				'is_guest' =>			array('type' => 'bool', 'default' => false),
				'active' =>				array('type' => 'bool', 'default' => true),
			)
		);
	}

	protected function installGuest($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Guest',
			$identifier,
			$data,
			array(
				'id_operating_system' =>	array('type' => 'int', 'default' => 0),
				'operating_system' =>		array('type' => 'relation_table', 'field' => 'id_operating_system', 'table' => 'operating_system', 'target_field' => 'name', 'default' => ''),
				'id_web_browser' =>			array('type' => 'int', 'default' => 0),
				'web_browser' =>			array('type' => 'relation_table', 'field' => 'id_web_browser', 'table' => 'web_browser', 'target_field' => 'name', 'default' => ''),
				'id_customer' =>			array('type' => 'int', 'default' => 0),
				'customer' =>				array('type' => 'relation', 'field' => 'id_customer', 'entity' => 'customer', 'default' => ''),
				'screen_resolution_x' =>	array('type' => 'int', 'default' => 0),
				'screen_resolution_y' =>	array('type' => 'int', 'default' => 0),
				'screen_color' =>			array('type' => 'int', 'default' => 0),
				'javascript' =>				array('type' => 'bool', 'default' => true),
				'sun_java' =>				array('type' => 'bool', 'default' => false),
				'adobe_flash' =>			array('type' => 'bool', 'default' => false),
				'adobe_director' =>			array('type' => 'bool', 'default' => false),
				'apple_quicktime' =>		array('type' => 'bool', 'default' => false),
				'real_player' =>			array('type' => 'bool', 'default' => false),
				'windows_media' =>			array('type' => 'bool', 'default' => false),
				'accept_language' =>		array('type' => 'string', 'default' => ''),
				'id_shop' =>				array('type' => 'int', 'default' => Context::getContext()->shop->id),
			)
		);

		if (isset($data['connections']) && is_array($data['connections']))
		{
			foreach ($data['connections'] as $connection_data)
			{
				if (isset($connection_data['id']))
				{
					$connection_data['id_guest'] = $object->id;
					$this->installConnection($connection_data['id'], $connection_data);
				}
			}
		}
	}

	protected function installConnection($identifier, array $data)
	{
		if (isset($data['ip_address']))
			$data['ip_address'] = ip2long($data['ip_address']);

		$object = $this->installGenericEntity(
			'Connection',
			$identifier,
			$data,
			array(
				'id_guest' =>		array('type' => 'int', 'default' => 0),
				'guest' =>			array('type' => 'relation', 'field' => 'id_guest', 'entity' => 'guest', 'default' => ''),
				'id_page' =>		array('type' => 'int', 'default' => 0),
				'ip_address' =>		array('type' => 'string', 'default' => ''),
				'http_referer' =>	array('type' => 'string', 'default' => ''),
				'id_shop' =>		array('type' => 'int', 'default' => Context::getContext()->shop->id),
				'id_group_shop' =>	array('type' => 'int', 'default' => Context::getContext()->shop->getGroupID()),
			)
		);
	}

	protected function installAddress($identifier, array $data)
	{
		if (isset($data['country']))
			$data['id_country'] = Country::getByIso($data['country']);

		if (isset($data['state']))
		{
			$state = $data['state'];
			$data['id_state'] = State::getIdByName($state);
			if (!$data['id_state'])
				$data['id_state'] = State::getIdByIso($state);
		}

		$object = $this->installGenericEntity(
			'Address',
			$identifier,
			$data,
			array(
				'firstname' =>		array('type' => 'string', 'default' => ''),
				'lastname' =>		array('type' => 'string', 'default' => ''),
				'address1' =>		array('type' => 'string', 'default' => ''),
				'address2' =>		array('type' => 'string', 'default' => ''),
				'postcode' =>		array('type' => 'string', 'default' => ''),
				'city' =>			array('type' => 'string', 'default' => ''),
				'phone' =>			array('type' => 'string', 'default' => ''),
				'alias' =>			array('type' => 'string', 'default' => ''),
				'company' =>		array('type' => 'string', 'default' => ''),
				'id_country' =>		array('type' => 'int', 'default' => 0),
				'id_state' =>		array('type' => 'int', 'default' => 0),
				'id_customer' =>	array('type' => 'int', 'default' => 0),
				'customer' =>		array('type' => 'relation', 'field' => 'id_customer', 'entity' => 'customer', 'default' => ''),
				'id_manufacturer' =>array('type' => 'int', 'default' => 0),
				'manufacturer' =>	array('type' => 'relation', 'field' => 'id_manufacturer', 'entity' => 'manufacturer', 'default' => ''),
				'id_supplier' =>	array('type' => 'int', 'default' => 0),
				'supplier' =>		array('type' => 'relation', 'field' => 'id_supplier', 'entity' => 'supplier', 'default' => ''),
				'active' =>			array('type' => 'bool', 'default' => true),
			)
		);
	}

	protected function installStore($identifier, array $data)
	{
		if (isset($data['country']))
			$data['id_country'] = Country::getByIso($data['country']);

		if (isset($data['state']))
		{
			$state = $data['state'];
			$data['id_state'] = State::getIdByName($state);
			if (!$data['id_state'])
				$data['id_state'] = State::getIdByIso($state);
		}

		if (isset($data['hours']) && is_array($data['hours']))
			$data['hours'] = serialize($data['hours']);

		$object = $this->installGenericEntity(
			'Store',
			$identifier,
			$data,
			array(
				'id_country' =>		array('type' => 'int', 'default' => 0),
				'id_state' =>		array('type' => 'int', 'default' => 0),
				'name' =>			array('type' => 'string', 'default' => ''),
				'address1' =>		array('type' => 'string', 'default' => ''),
				'address2' =>		array('type' => 'string', 'default' => ''),
				'city' =>			array('type' => 'string', 'default' => ''),
				'postcode' =>		array('type' => 'string', 'default' => ''),
				'latitude' =>		array('type' => 'float', 'default' => 0.00),
				'longitude' =>		array('type' => 'float', 'default' => 0.00),
				'hours' =>			array('type' => 'string', 'default' => ''),
				'phone' =>			array('type' => 'string', 'default' => ''),
				'fax' =>			array('type' => 'string', 'default' => ''),
				'email' =>			array('type' => 'string', 'default' => ''),
				'note' =>			array('type' => 'string', 'default' => ''),
				'active' =>			array('type' => 'bool', 'default' => true),
			)
		);
	}

	protected function installCategory($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Category',
			$identifier,
			$data,
			array(
				'id_parent' =>		array('type' => 'int', 'default' => 1),
				'parent' =>			array('type' => 'relation', 'field' => 'id_parent', 'entity' => 'category', 'default' => 0),
				'active' =>			array('type' => 'bool', 'default' => true),
			),
			array('name', 'description', 'link_rewrite', 'meta_title', 'meta_keyword', 'meta_description')
		);

		$this->createEntityImages('category', $identifier, $object->id, 'categories', 'c');
	}

	protected function installFeature($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Feature',
			$identifier,
			$data,
			array(),
			array('name')
		);
	}

	protected function installFeatureValue($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'FeatureValue',
			$identifier,
			$data,
			array(
				'id_feature' =>	array('type' => 'int', 'default' => 1),
				'feature' =>	array('type' => 'relation', 'field' => 'id_feature', 'entity' => 'feature', 'default' => 0),
				'custom' =>		array('type' => 'bool', 'default' => false),
			),
			array('value')
		);
	}

	protected function installAttributeGroup($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'AttributeGroup',
			$identifier,
			$data,
			array(
				'group_type' =>		array('type' => 'string', 'default' => 'select'),
				'is_color_group' =>	array('type' => 'bool', 'default' => false),
			),
			array('name', 'public_name')
		);
	}

	protected function installAttribute($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Attribute',
			$identifier,
			$data,
			array(
				'id_attribute_group' =>	array('type' => 'int', 'default' => 1),
				'attribute_group' =>	array('type' => 'relation', 'field' => 'id_attribute_group', 'entity' => 'attributegroup', 'default' => 0),
				'color' =>				array('type' => 'string', 'default' => ''),
			),
			array('name', 'public_name')
		);
	}

	protected function installProduct($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Product',
			$identifier,
			$data,
			array(
				'id_supplier' =>		array('type' => 'int', 'default' => 0),
				'supplier' =>			array('type' => 'relation', 'field' => 'id_supplier', 'entity' => 'supplier', 'default' => 0),
				'id_manufacturer' =>	array('type' => 'int', 'default' => 0),
				'manufacturer' =>		array('type' => 'relation', 'field' => 'id_manufacturer', 'entity' => 'manufacturer', 'default' => 0),
				'id_tax_rules_group' =>	array('type' => 'int', 'default' => 0),
				'id_category_default' =>array('type' => 'int', 'default' => 1),
				'category_default' =>	array('type' => 'relation', 'field' => 'id_category_default', 'entity' => 'category', 'default' => 0),
				'on_sale' =>			array('type' => 'int', 'default' => 0),
				'online_only' =>		array('type' => 'int', 'default' => 0),
				'ecotax' =>				array('type' => 'float', 'default' => 0.00),
				'price' =>				array('type' => 'float', 'default' => 0.00),
				'wholesale_price' =>	array('type' => 'float', 'default' => 0.00),
				'ean13' =>				array('type' => 'string', 'default' => ''),
				'reference' =>			array('type' => 'string', 'default' => ''),
				'supplier_reference' =>	array('type' => 'string', 'default' => ''),
				'weight' =>				array('type' => 'int', 'default' => 0),
				'out_of_stock' =>		array('type' => 'int', 'default' => 2),
				'quantity_discount' =>	array('type' => 'int', 'default' => 0),
				'customizable' =>		array('type' => 'int', 'default' => 0),
				'uploadable_files' =>	array('type' => 'int', 'default' => 0),
				'text_fields' =>		array('type' => 'int', 'default' => 0),
				'available_date' =>		array('type' => 'date', 'default' => '0000-00-00'),
				'indexed' =>			array('type' => 'bool', 'default' => true),
				'active' =>				array('type' => 'bool', 'default' => true),
			),
			array('description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords', 'meta_title', 'name', 'available_now', 'available_later')
		);

		if ($object)
		{
			// Add product to categories
			if (isset($data['categories']) && is_array($data['categories']))
			{
				$categories = array();
				foreach ($data['categories'] as $category)
					$categories = (is_array($category)) ? $this->getId('category', $category['id']) : (int)$category;
				$object->addToCategories($categories);
			}

			// Add features to the product
			if (isset($data['features']) && is_array($data['features']))
			{
				static $cache_features = array();

				foreach ($data['features'] as $id_feature_value)
				{
					$id_feature_value = (is_array($id_feature_value)) ? $this->getId('featurevalue', $id_feature_value['id']) : (int)$id_feature_value;
					if (!isset($cache_features[$id_feature_value]))
						$cache_features[$id_feature_value] = $this->db->getValue('
							SELECT id_feature
							FROM '._DB_PREFIX_.'feature_value
							WHERE id_feature_value = '.(int)$id_feature_value
						);
					Product::addFeatureProductImport($object->id, $cache_features[$id_feature_value], $id_feature_value);
				}
			}

			// Add specific price
			if (isset($data['specific_prices']) && is_array($data['specific_prices']))
			{
				foreach ($data['specific_prices'] as $price_data)
				{
					$price_data['id_product'] = $object->id;
					$this->installSpecificPrice($price_data['id'], $price_data);
				}
			}

			// Add images
			if (isset($data['images']) && is_array($data['images']))
			{
				foreach ($data['images'] as $image_data)
				{
					$image_data['id_product'] = $object->id;
					$this->installImage($image_data['id'], $image_data);
				}
			}

			// Add combinations
			if (isset($data['combinations']) && is_array($data['combinations']))
			{
				foreach ($data['combinations'] as $combination_data)
				{
					$combination_data['id_product'] = $object->id;
					$this->installCombination($combination_data['id'], $combination_data);
				}
			}
		}
	}


	protected function installSpecificPrice($identifier, array $data)
	{
		if (isset($data['country']))
			$data['id_country'] = Country::getByIso($data['country']);

		if (isset($data['currency']))
			$data['id_currency'] = Currency::getIdByIsoCode($data['currency']);

		$object = $this->installGenericEntity(
			'SpecificPrice',
			$identifier,
			$data,
			array(
				'id_product' =>			array('type' => 'int', 'default' => 0),
				'product' =>			array('type' => 'relation', 'field' => 'id_product', 'entity' => 'product', 'default' => 0),
				'id_shop' =>			array('type' => 'int', 'default' => 0),
				'id_currency' =>		array('type' => 'int', 'default' => 0),
				'id_country' =>			array('type' => 'int', 'default' => 0),
				'id_group' =>			array('type' => 'int', 'default' => 0),
				'price' =>				array('type' => 'float', 'default' => 0),
				'from_quantity' =>		array('type' => 'int', 'default' => 0),
				'reduction' =>			array('type' => 'float', 'default' => 0),
				'reduction_type' =>		array('type' => 'string', 'default' => 'percentage'),
				'from' =>				array('type' => 'date', 'default' => '0000-00-00 00:00:00'),
				'to' =>					array('type' => 'date', 'default' => '0000-00-00 00:00:00'),
			)
		);
	}

	protected function installImage($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Image',
			$identifier,
			$data,
			array(
				'id_product' =>	array('type' => 'int', 'default' => 0),
				'product' =>	array('type' => 'relation', 'field' => 'id_product', 'entity' => 'product', 'default' => 0),
				'cover' =>		array('type' => 'bool', 'default' => false),
			),
			array('legend')
		);

		$this->createProductImages($object, $identifier, $object->id_product);
	}

	protected function installCombination($identifier, array $data)
	{
		if (!isset($data['id_product']))
			return;

		$d = $this->getHydratedData($data, array(
			'reference' =>			array('type' => 'string', 'default' => ''),
			'supplier_reference' =>	array('type' => 'string', 'default' => ''),
			'ean13' =>				array('type' => 'string', 'default' => ''),
			'price' =>				array('type' => 'float', 'default' => 0.000000),
			'ecotax' =>				array('type' => 'float', 'default' => 0.00),
			'weight' =>				array('type' => 'float', 'default' => 0),
			'default_on' =>			array('type' => 'bool', 'default' => false),
			'unit_price_impact' =>	array('type' => 'float', 'default' => 0.00),
		));

		$product = new Product($data['id_product']);
		if (!Validate::isLoadedObject($product))
			return;

		$images = array();
		if (isset($data['images']) && is_array($data['images']))
		{
			$images = array();
			foreach ($data['images'] as $image)
				$images[] = (is_array($image)) ? $this->getId('image', $image['id']) : (int)$image;
		}

		$id_product_attribute = $product->addProductAttribute(
			$d['price'],
			$d['weight'],
			$d['unit_price_impact'],
			$d['ecotax'],
			$images,
			$d['reference'],
			$d['supplier_reference'],
			$d['ean13'],
			$d['default_on']
		);

		if (!$id_product_attribute)
		{
			$this->setError($this->language->l('Cannot create product attribute'));
			return false;
		}

		if (isset($data['attributes']) && is_array($data['attributes']))
		{
			$attributes = array();
			foreach ($data['attributes'] as $attribute)
				$attributes[] = (is_array($attribute)) ? $this->getId('attribute', $attribute['id']) : (int)$attribute;
			$product->addAttributeCombinaison($id_product_attribute, $attributes);
		}


		$this->ids['combination:'.$identifier] = $id_product_attribute;
	}

	protected function installScene($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'Scene',
			$identifier,
			$data,
			array(
				'active' =>	array('type' => 'bool', 'default' => true),
			),
			array('legend')
		);

		// Add categories to scene
		if (isset($data['categories']) && is_array($data['categories']))
		{
			$categories = array();
			foreach ($data['categories'] as $category)
				$categories[] = (is_array($category)) ? $this->getId('categories', $category['id']) : (int)$category;
			$object->addCategories($categories);
		}

		// Add products to scene
		if (isset($data['zones']) && is_array($data['zones']))
		{
			$zones = array();
			foreach ($data['zones'] as $zone)
			{
				if (isset($zone['product']) && !isset($zone['id_product']))
					$zone['id_product'] = $this->getId('product', $zone['product']);
				$zones[] = $zone;
			}
			$object->addZoneProducts($zones);
		}

		$this->createEntityImages('scene', $identifier, $object->id, 'scenes', 'scenes');
	}

	protected function installRange($identifier, array $data)
	{
		$class = (isset($data) && $data['type'] == 'price') ? 'RangePrice' : 'RangeWeight';
		$object = $this->installGenericEntity(
			$class,
			$identifier,
			$data,
			array(
				'id_carrier' =>	array('type' => 'int', 'default' => 0),
				'carrier' =>	array('type' => 'relation', 'field' => 'id_carrier', 'entity' => 'carrier', 'default' => 0),
				'delimiter1' =>	array('type' => 'int', 'default' => 0),
				'delimiter2' =>	array('type' => 'int', 'default' => 0),
			)
		);
	}

	protected function installDelivery($identifier, array $data)
	{
		if (isset($data['zone']))
			$data['id_zone'] = Zone::getIdByName($data['zone']);

		$object = $this->installGenericEntity(
			'Delivery',
			$identifier,
			$data,
			array(
				'id_range_price' =>	array('type' => 'int', 'default' => 0),
				'range_price' =>	array('type' => 'relation', 'field' => 'id_range_price', 'entity' => 'rangeprice', 'default' => 0),
				'id_range_weight' =>array('type' => 'int', 'default' => 0),
				'range_weight' =>	array('type' => 'relation', 'field' => 'id_range_weight', 'entity' => 'rangeweight', 'default' => 0),
				'id_carrier' =>		array('type' => 'int', 'default' => 0),
				'carrier' =>		array('type' => 'relation', 'field' => 'id_carrier', 'entity' => 'carrier', 'default' => 0),
				'id_zone' =>		array('type' => 'int', 'default' => 0),
				'price' =>			array('type' => 'float', 'default' => 0.00),
			)
		);
	}

	protected function installCart($identifier, array $data)
	{
		if (isset($data['lang']))
			$data['id_lang'] = Language::getIdByIso($data['lang']);

		if (isset($data['currency']))
			$data['id_currency'] = Currency::getIdByIsoCode($data['currency']);

		$object = $this->installGenericEntity(
			'Cart',
			$identifier,
			$data,
			array(
				'id_carrier' =>				array('type' => 'int', 'default' => 0),
				'carrier' =>				array('type' => 'relation', 'field' => 'id_carrier', 'entity' => 'carrier', 'default' => 0),
				'id_lang' =>				array('type' => 'int', 'default' => Configuration::get('PS_LANG_DEFAULT')),
				'id_address_delivery' =>	array('type' => 'int', 'default' => 0),
				'address_delivery' =>		array('type' => 'relation', 'field' => 'id_address_delivery', 'entity' => 'address', 'default' => 0),
				'id_address_invoice' =>		array('type' => 'int', 'default' => 0),
				'address_invoice' =>		array('type' => 'relation', 'field' => 'id_address_invoice', 'entity' => 'address', 'default' => 0),
				'id_currency' =>			array('type' => 'int', 'default' => Configuration::get('PS_CURRENCY_DEFAULT')),
				'id_customer' =>			array('type' => 'int', 'default' => 0),
				'customer' =>				array('type' => 'relation', 'field' => 'id_customer', 'entity' => 'customer', 'default' => 0),
				'id_guest' =>				array('type' => 'int', 'default' => 0),
				'guest' =>					array('type' => 'relation', 'field' => 'id_guest', 'entity' => 'guest', 'default' => 0),
				'id_shop' => 				array('type' => 'int', 'default' => Context::getContext()->shop->id),
				'recyclable' =>				array('type' => 'bool', 'default' => false),
				'gift' =>					array('type' => 'bool', 'default' => false),
			)
		);

		// Add products to cart
		if ($object && $data['products'] && is_array($data['products']))
		{
			foreach ($data['products'] as $product)
			{
				if (isset($product['product']))
					$product['id_product'] = $this->getId('product', $product['product']);

				if (isset($product['combination']))
					$product['id_product_attribute'] = $this->getId('combination', $product['combination']);

				$this->db->autoExecute(_DB_PREFIX_.'cart_product', array(
					'id_product' => 			(isset($product['id_product'])) ? (int)$product['id_product'] : 0,
					'id_product_attribute' => 	(isset($product['id_product_attribute'])) ? (int)$product['id_product_attribute'] : 0,
					'id_cart' => 				(int)$object->id,
					'id_shop' => 				(isset($product['id_shop'])) ? $product['id_shop'] : Context::getContext()->shop->id,
					'quantity' => 				(isset($product['quantity'])) ? (int)$product['quantity'] : 1,
					'date_add' => 				date('Y-m-d H:i:s')
				), 'INSERT');
			}
		}
	}

	protected function installOrder($identifier, array $data)
	{
		if (isset($data['currency']))
			$data['id_currency'] = Currency::getIdByIsoCode($data['currency']);

		$object = $this->installGenericEntity(
			'Order',
			$identifier,
			$data,
			array(
				'id_lang' =>				array('type' => 'int', 'default' => Configuration::get('PS_LANG_DEFAULT')),
				'id_shop' => 				array('type' => 'int', 'default' => Context::getContext()->shop->id),
				'id_group_shop' => 			array('type' => 'int', 'default' => Context::getContext()->shop->getGroupID()),
				'id_carrier' =>				array('type' => 'int', 'default' => Configuration::get('PS_CARRIER_DEFAULT')),
				'carrier' =>				array('type' => 'relation', 'field' => 'id_carrier', 'entity' => 'carrier', 'default' => 0),
				'id_customer' =>			array('type' => 'int', 'default' => 0),
				'customer' =>				array('type' => 'relation', 'field' => 'id_customer', 'entity' => 'customer', 'default' => 0),
				'id_cart' =>				array('type' => 'int', 'default' => 0),
				'cart' =>					array('type' => 'relation', 'field' => 'id_cart', 'entity' => 'cart', 'default' => 0),
				'id_currency' =>			array('type' => 'int', 'default' => Configuration::get('PS_CURRENCY_DEFAULT')),
				'id_address_delivery' =>	array('type' => 'int', 'default' => 0),
				'address_delivery' =>		array('type' => 'relation', 'field' => 'id_address_delivery', 'entity' => 'address', 'default' => 0),
				'id_address_invoice' =>		array('type' => 'int', 'default' => 0),
				'address_invoice' =>		array('type' => 'relation', 'field' => 'id_address_invoice', 'entity' => 'address', 'default' => 0),
				'secure_key' =>				array('type' => 'string', 'default' => ''),
				'payment' =>				array('type' => 'string', 'default' => ''),
				'module' =>					array('type' => 'string', 'default' => ''),
				'recyclable' =>				array('type' => 'bool', 'default' => false),
				'gift' =>					array('type' => 'bool', 'default' => false),
				'gift_message' =>			array('type' => 'string', 'default' => ''),
				'shipping_number' =>		array('type' => 'string', 'default' => ''),
				'total_discounts' =>		array('type' => 'float', 'default' => 0.00),
				'total_paid' =>				array('type' => 'float', 'default' => 0.00),
				'total_paid_real' =>		array('type' => 'float', 'default' => 0.00),
				'total_products' =>			array('type' => 'float', 'default' => 0.00),
				'total_products_wt' =>		array('type' => 'float', 'default' => 0.00),
				'total_shipping' =>			array('type' => 'float', 'default' => 0.00),
				'conversion_rate' =>		array('type' => 'float', 'default' => 1.000000),
				'invoice_number' =>			array('type' => 'int', 'default' => 0),
				'delivery_number' =>		array('type' => 'int', 'default' => 0),
				'invoice_date' =>			array('type' => 'date', 'default' => '0000-00-00 00:00:00'),
				'delivery_date' =>			array('type' => 'date', 'default' => '0000-00-00 00:00:00'),
				'valid' =>					array('type' => 'bool', 'default' => false),
			)
		);

		if ($object)
		{
			// Add order details
			if (isset($data['details']) && is_array($data['details']))
			{
				foreach ($data['details'] as $detail_data)
				{
					$detail_data['id_order'] = $object->id;
					$this->installOrderDetail($detail_data['id'], $detail_data);
				}
			}

			// Add order history
			if (isset($data['histories']) && is_array($data['histories']))
			{
				foreach ($data['histories'] as $history_data)
				{
					$history_data['id_order'] = $object->id;
					$this->installOrderHistory($history_data['id'], $history_data);
				}
			}
		}
	}

	protected function installOrderDetail($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'OrderDetail',
			$identifier,
			$data,
			array(
				'id_order' =>					array('type' => 'int', 'default' => 0),
				'order' =>						array('type' => 'relation', 'field' => 'id_order', 'entity' => 'order', 'default' => 0),
				'product_id' =>					array('type' => 'int', 'default' => 0),
				'product' =>					array('type' => 'relation', 'field' => 'product_id', 'entity' => 'product', 'default' => 0),
				'product_attribute_id' =>		array('type' => 'int', 'default' => 0),
				'combination' =>				array('type' => 'relation', 'field' => 'product_attribute_id', 'entity' => 'combination', 'default' => 0),
				'product_name' =>				array('type' => 'string', 'default' => ''),
				'product_quantity' =>			array('type' => 'int', 'default' => 1),
				'product_quantity_return' =>	array('type' => 'int', 'default' => 0),
				'product_price' =>				array('type' => 'float', 'default' => 0.000000),
				'product_quantity_discount' =>	array('type' => 'float', 'default' => 0.000000),
				'product_ean13' =>				array('type' => 'string', 'default' => ''),
				'product_reference' =>			array('type' => 'string', 'default' => ''),
				'product_supplier_reference' =>	array('type' => 'string', 'default' => ''),
				'product_weight' =>				array('type' => 'int', 'default' => 0),
				'ecotax' =>						array('type' => 'float', 'default' => 0.00),
				'download_hash' =>				array('type' => 'string', 'default' => ''),
				'download_nb' =>				array('type' => 'int', 'default' => 0),
				'download_deadline' =>			array('type' => 'date', 'default' => '0000-00-00 00:00:00'),
			)
		);
	}

	protected function installOrderHistory($identifier, array $data)
	{
		$object = $this->installGenericEntity(
			'OrderHistory',
			$identifier,
			$data,
			array(
				'id_order' =>					array('type' => 'int', 'default' => 0),
				'order' =>						array('type' => 'relation', 'field' => 'id_order', 'entity' => 'order', 'default' => 0),
				'id_employee' =>				array('type' => 'int', 'default' => 0),
				'id_order_state' =>				array('type' => 'int', 'default' => 0),
			)
		);
	}
}