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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class TagCore extends ObjectModel
{
 	/** @var integer Language id */
	public 		$id_lang;
	
 	/** @var string Name */
	public 		$name;
	
 	protected 	$fieldsRequired = array('id_lang', 'name');
 	protected 	$fieldsValidate = array('id_lang' => 'isUnsignedId', 'name' => 'isGenericName');

	protected 	$table = 'tag';
	protected 	$identifier = 'id_tag';
	
	protected	$webserviceParameters = array(
	'fields' => array(
	'id_lang' => array('xlink_resource' => 'languages'),
	),
	);
	
	public function __construct($id = NULL, $name = NULL, $id_lang = NULL)
	{
		if ($id)
			parent::__construct($id);
		elseif ($name AND Validate::isGenericName($name) AND $id_lang AND Validate::isUnsignedId($id_lang))
		{
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'tag` t
			WHERE `name` LIKE \''.pSQL($name).'\' AND `id_lang` = '.(int)($id_lang));
			
			if ($row)
			{
			 	$this->id = (int)($row['id_tag']);
			 	$this->id_lang = (int)($row['id_lang']);
				$this->name = $row['name'];
			}
		}
	}
		
	public function getFields()
	{
		parent::validateFields();
		$fields['id_lang'] = (int)($this->id_lang);
		$fields['name'] = pSQL($this->name);
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues))
			return false;
		elseif (isset($_POST['products']))
			return $this->setProducts(Tools::getValue('products'));
		return true;		
	}
	
	/**
	* Add several tags in database and link it to a product
	*
	* @param integer $id_lang Language id
	* @param integer $id_product Product id to link tags with
	* @param string $string Tags separated by commas
	*
	* @return boolean Operation success
	*/
	static public function addTags($id_lang, $id_product, $string)
	{
	 	if (!Validate::isUnsignedId($id_lang) OR !Validate::isTagsList($string))
			return false;
	 	
	 	$tmpTab = array_unique(array_map('trim', preg_split('/,/', $string, NULL, PREG_SPLIT_NO_EMPTY)));
	 	$list = array();
	 	foreach ($tmpTab AS $tag)
	 	{
	 	 	if (!Validate::isGenericName($tag))
	 	 		return false;
			$tagObj = new Tag(NULL, trim($tag), (int)($id_lang));
			
			/* Tag does not exist in database */
			if (!Validate::isLoadedObject($tagObj))
			{
				$tagObj->name = trim($tag);
				$tagObj->id_lang = (int)($id_lang);
				$tagObj->add();
			}
			if (!in_array($tagObj->id, $list))
				$list[] = $tagObj->id;
		}
		$data = '';
		foreach ($list AS $tag)
			$data .= '('.(int)($tag).','.(int)($id_product).'),';
		$data = rtrim($data, ',');

		return Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'product_tag` (`id_tag`, `id_product`) 
		VALUES '.$data);
	}
	
	static public function getMainTags($id_lang, $nb = 10)
	{
		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT t.name, COUNT(pt.id_tag) AS times
		FROM `'._DB_PREFIX_.'product_tag` pt
		LEFT JOIN `'._DB_PREFIX_.'tag` t ON (t.id_tag = pt.id_tag)
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = pt.id_product)
		WHERE t.`id_lang` = '.(int)($id_lang).'
		AND p.`active` = 1
		AND p.`id_product` IN (
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_group` cg
			LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
			WHERE cg.`id_group` '.$sqlGroups.'
		)
		GROUP BY t.id_tag
		ORDER BY times DESC
		LIMIT 0, '.(int)($nb));
	}
	
	static public function getProductTags($id_product)
	{
	 	if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT t.`id_lang`, t.`name` 
		FROM '._DB_PREFIX_.'tag t 
		LEFT JOIN '._DB_PREFIX_.'product_tag pt ON (pt.id_tag = t.id_tag) 
		WHERE pt.`id_product`='.(int)($id_product)))
	 		return false;
	 	$result = array();
	 	foreach ($tmp AS $tag)
	 		$result[$tag['id_lang']][] = $tag['name'];
	 	return $result;
	}
	
	public function getProducts($associated = true)
	{
		global $cookie;
		$id_lang = $this->id_lang ? $this->id_lang : $cookie->id_lang;
		
		if (!$this->id AND $associated)
			return array();
		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT pl.name, pl.id_product
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON p.id_product = pl.id_product
		WHERE pl.id_lang = '.(int)($id_lang).'
		AND p.active = 1
		'.($this->id ? ('AND p.id_product '.($associated ? 'IN' : 'NOT IN').' (SELECT pt.id_product FROM `'._DB_PREFIX_.'product_tag` pt WHERE pt.id_tag = '.(int)($this->id).')') : '').'
		ORDER BY pl.name');
	}
	
	public function setProducts($array)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'product_tag WHERE id_tag = '.(int)$this->id);
		if (is_array($array))
		{
			$array = array_map('intval', $array);
			$result1 = Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET indexed = 0 WHERE id_product IN ('.implode(',', $array).')');
			$ids = array();
			foreach ($array as $id_product)
				$ids[] = '('.(int)$id_product.','.(int)$this->id.')';
			return ($result1 && Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'product_tag (id_product, id_tag) VALUES '.implode(',',$ids)) && Search::indexation(false));
		}
		return $result1;
	}
	
	static public function deleteTagsForProduct($id_product)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_product` = '.(int)($id_product));
	}
}


