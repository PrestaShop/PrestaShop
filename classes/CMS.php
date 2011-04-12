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

class CMSCore extends ObjectModel
{
	
	/** @var string Name */
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $content;
	public $link_rewrite;
	public $id_cms_category;
	public $position;
	public $active;

 	protected $fieldsRequiredLang = array('meta_title', 'link_rewrite');
	protected $fieldsSizeLang = array('meta_description' => 255, 'meta_keywords' => 255, 'meta_title' => 128, 'link_rewrite' => 128, 'content' => 3999999999999);
	protected $fieldsValidateLang = array('meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName', 'meta_title' => 'isGenericName', 'link_rewrite' => 'isLinkRewrite', 'content' => 'isString');

	protected $table = 'cms';
	protected $identifier = 'id_cms';
	
	public function getFields() 
	{ 
		parent::validateFields();
		$fields['id_cms'] = (int)($this->id);
		$fields['id_cms_category'] = (int)($this->id_cms_category);
		$fields['position'] = (int)($this->position);
		$fields['active'] = (int)($this->active);
		return $fields;	 
	}
	
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();

		$fieldsArray = array('meta_title', 'meta_description', 'meta_keywords', 'link_rewrite');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = (int)($language['id_lang']);
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			$fields[$language['id_lang']]['content'] = (isset($this->content[$language['id_lang']])) ? pSQL($this->content[$language['id_lang']], true) : '';
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']]);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false)
	{ 
		$this->position = CMS::getLastPosition((int)(Tools::getValue('id_cms_category')));
		return parent::add($autodate, true); 
	}
	
	public function update($nullValues = false)
	{
		if (parent::update($nullValues))
			return $this->cleanPositions($this->id_cms_category);
		return false;
	}
	
	public function delete()
	{
	 	if (parent::delete())
			return $this->cleanPositions($this->id_cms_category);
		return false;
	}

	public static function getLinks($id_lang, $selection = NULL, $active = true)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_cms, cl.link_rewrite, cl.meta_title
		FROM '._DB_PREFIX_.'cms c
		LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '.(int)($id_lang).')
		WHERE 1
		'.(($selection !== NULL) ? ' AND c.id_cms IN ('.implode(',', array_map('intval', $selection)).')' : '').
		($active ? ' AND c.`active` = 1 ' : '').
		'ORDER BY c.`position`');
		$link = new Link();
		$links = array();
		if ($result)
			foreach ($result as $row)
			{
				$row['link'] = $link->getCMSLink((int)($row['id_cms']), $row['link_rewrite']);
				$links[] = $row;
			}
		return $links;
	}
	
	public static function listCms($id_lang = NULL, $id_block = false, $active = true)
	{
		if (empty($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_cms, l.meta_title
		FROM  '._DB_PREFIX_.'cms c
		JOIN '._DB_PREFIX_.'cms_lang l ON (c.id_cms = l.id_cms)
		'.(($id_block) ? 'JOIN '._DB_PREFIX_.'block_cms b ON (c.id_cms = b.id_cms)' : '').'
		WHERE l.id_lang = '.(int)($id_lang).(($id_block) ? ' AND b.id_block = '.(int)($id_block) : '').($active ? ' AND c.`active` = 1 ' : '').'
		ORDER BY c.`position`');
	}
	
	/**
	 * @deprecated
	 */
	public static function isInBlock($id_cms, $id_block)
	{
		Tools::displayAsDeprecated();
		Db::getInstance()->getRow('
		SELECT id_cms FROM '._DB_PREFIX_.'block_cms
		WHERE id_block = '.(int)($id_block).' AND id_cms = '.(int)($id_cms));
		
		return (Db::getInstance()->NumRows());
	}
	
	/**
	 * @deprecated
	 */
	public static function updateCmsToBlock($cms, $id_block)
	{
		Tools::displayAsDeprecated();
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'block_cms` WHERE `id_block` = '.(int)($id_block));

		$list = '';
		foreach ($cms AS $id_cms)
			$list .= '('.(int)($id_block).', '.(int)($id_cms).'),';
		$list = rtrim($list, ',');
		
		if (!empty($list))
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'block_cms (id_block, id_cms) VALUES '.pSQL($list));
			
		return true;
	}
	
	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->ExecuteS('
			SELECT cp.`id_cms`, cp.`position`, cp.`id_cms_category` 
			FROM `'._DB_PREFIX_.'cms` cp
			WHERE cp.`id_cms_category` = '.(int)(Tools::getValue('id_cms_category', 1)).' 
			ORDER BY cp.`position` ASC'
		))
			return false;
		
		foreach ($res AS $cms)
			if ((int)($cms['id_cms']) == (int)($this->id))
				$movedCms = $cms;
		
		if (!isset($movedCms) || !isset($position))
			return false;
		
		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'cms`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position` 
			'.($way 
				? '> '.(int)($movedCms['position']).' AND `position` <= '.(int)($position)
				: '< '.(int)($movedCms['position']).' AND `position` >= '.(int)($position)).'
			AND `id_cms_category`='.(int)($movedCms['id_cms_category']))
		AND Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'cms`
			SET `position` = '.(int)($position).'
			WHERE `id_cms` = '.(int)($movedCms['id_cms']).'
			AND `id_cms_category`='.(int)($movedCms['id_cms_category'])));
	}
	
	static public function cleanPositions($id_category)
	{
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_cms`
		FROM `'._DB_PREFIX_.'cms`
		WHERE `id_cms_category` = '.(int)($id_category).'
		ORDER BY `position`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; ++$i){
				$sql = '
				UPDATE `'._DB_PREFIX_.'cms`
				SET `position` = '.(int)($i).'
				WHERE `id_cms_category` = '.(int)($id_category).'
				AND `id_cms` = '.(int)($result[$i]['id_cms']);
				Db::getInstance()->Execute($sql);
			}
		return true;
	}
	
	static public function getLastPosition($id_category)
	{
		return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'cms` WHERE `id_cms_category` = '.(int)($id_category)));
	}
	
	static public function getCMSPages($id_lang = NULL, $id_cms_category = NULL, $active = true)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'cms` c
		JOIN `'._DB_PREFIX_.'cms_lang` l ON (c.id_cms = l.id_cms)'.
		(isset($id_cms_category) ? 'WHERE `id_cms_category` = '.(int)($id_cms_category) : '').
		($active ? ' AND c.`active` = 1 ' : '').'
		AND l.id_lang = '.(int)($id_lang).'
		ORDER BY `position`');
	}
    public static function getUrlRewriteInformations($id_cms)
	{
	    $sql = '
		SELECT l.`id_lang`, c.`link_rewrite`
		FROM `'._DB_PREFIX_.'cms_lang` AS c
		LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
		WHERE c.`id_cms` = '.(int)$id_cms.'
		AND l.`active` = 1';
		$arr_return = Db::getInstance()->ExecuteS($sql);
		return $arr_return;
	}
}

