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

class MetaCore extends ObjectModel
{
	/** @var string Name */
	public 		$page;
	
	public 		$title;
	public 		$description;
	public 		$keywords;
	public 		$url_rewrite;
	
 	protected 	$fieldsRequired = array('page');
 	protected 	$fieldsSize = array('page' => 64);
 	protected 	$fieldsValidate = array('page' => 'isFileName');
	
	protected	$fieldsRequiredLang = array();
	protected	$fieldsSizeLang = array('title' => 128, 'description' => 255, 'keywords' => 255, 'url_rewrite' => 255);
	protected	$fieldsValidateLang = array('title' => 'isGenericName', 'description' => 'isGenericName', 'keywords' => 'isGenericName', 'url_rewrite' => 'isLinkRewrite');
	
	protected 	$table = 'meta';
	protected 	$identifier = 'id_meta';
		
	public function getFields()
	{
		parent::validateFields();
		return array('page' => pSQL($this->page));
	}
	
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('title', 'description', 'keywords', 'url_rewrite'));
	}
	
	static public function getPages($excludeFilled = false, $addPage = false)
	{
		$selectedPages = array();
		if (!$files = scandir(_PS_ROOT_DIR_))
			die(Tools::displayError('Cannot scan root directory'));
		
		// Exclude pages forbidden
		$exludePages = array('category', 'changecurrency', 'cms', 'footer', 'header', 'images.inc', 'init',
		'pagination', 'product', 'product-sort', 'statistics');
		foreach ($files as $file)
			if (preg_match('/^[a-z0-9_.-]*\.php$/i', $file) AND !in_array(str_replace('.php', '', $file), $exludePages))
				$selectedPages[] = str_replace('.php', '', $file);
		// Exclude page already filled
		if ($excludeFilled)
		{
			$metas = self::getMetas();
			foreach ($metas as $k => $meta)
				if (in_array($meta['page'], $selectedPages))
					unset($selectedPages[array_search($meta['page'], $selectedPages)]);
		}
		// Add selected page
		if ($addPage)
		{
			$selectedPages[] = $addPage;
			sort($selectedPages);
		}
		return $selectedPages;
	}
	
	static public function getMetas()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'meta
		ORDER BY page ASC');
	}

	static public function getMetasByIdLang($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'meta` m
		LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON m.`id_meta` = ml.`id_meta`
		WHERE ml.`id_lang` = '.(int)($id_lang).' 
		ORDER BY page ASC');
		
	}
	
	static public function getMetaByPage($page, $id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT *
		FROM '._DB_PREFIX_.'meta m
		LEFT JOIN '._DB_PREFIX_.'meta_lang ml on (m.id_meta = ml.id_meta)
		WHERE m.page = \''.pSQL($page).'\' AND ml.id_lang = '.(int)($id_lang));
	}

	public function update($nullValues = false)
	{
		if (!parent::update($nullValues))
			return false;			
									
		return Tools::generateHtaccess(dirname(__FILE__).'/../.htaccess',
									(int)(Configuration::get('PS_REWRITING_SETTINGS')),		
									(int)(Configuration::get('PS_HTACCESS_CACHE_CONTROL')), 
									Configuration::get('PS_HTACCESS_SPECIFIC')
									);
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues));
		
		return Tools::generateHtaccess(dirname(__FILE__).'/../.htaccess',
									(int)(Configuration::get('PS_REWRITING_SETTINGS')),		
									(int)(Configuration::get('PS_HTACCESS_CACHE_CONTROL')), 
									Configuration::get('PS_HTACCESS_SPECIFIC')
									);
	}
	
	public function delete()
	{
		if (!parent::delete())
			return false;
		
		return Tools::generateHtaccess(dirname(__FILE__).'/../.htaccess',
								(int)(Configuration::get('PS_REWRITING_SETTINGS')),		
								(int)(Configuration::get('PS_HTACCESS_CACHE_CONTROL')), 
								Configuration::get('PS_HTACCESS_SPECIFIC')
								);
	}
	
	public function deleteSelection($selection)
	{
		if (!is_array($selection) OR !Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection AS $id)
		{
			$this->id = (int)($id);
			$result = $result AND $this->delete();
		}
		
		return Tools::generateHtaccess(dirname(__FILE__).'/../.htaccess',
									(int)(Configuration::get('PS_REWRITING_SETTINGS')),		
									(int)(Configuration::get('PS_HTACCESS_CACHE_CONTROL')), 
									Configuration::get('PS_HTACCESS_SPECIFIC')
									);
	}

	static public function getEquivalentUrlRewrite($new_id_lang, $id_lang, $url_rewrite)
	{
		return Db::getInstance()->getValue('
		SELECT url_rewrite
		FROM `'._DB_PREFIX_.'meta_lang`
		WHERE id_meta = (
			SELECT id_meta
			FROM `'._DB_PREFIX_.'meta_lang`
			WHERE url_rewrite = \''.pSQL($url_rewrite).'\' AND id_lang = '.(int)($id_lang).'
		)
		AND id_lang = '.(int)($new_id_lang));
	}
}

