<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7445 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MetaCore extends ObjectModel
{
	/** @var string Name */
	public $page;
	public $title;
	public $description;
	public $keywords;
	public $url_rewrite;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'meta',
		'primary' => 'id_meta',
		'multilang' => true,
		'multishop' => true,
		'fields' => array(
			'page' => 			array('type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true, 'size' => 64),

			// Lang fields
			'title' => 			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
			'description' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'keywords' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'url_rewrite' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 255),
		),
	);

	public static function getPages($exclude_filled = false, $add_page = false)
	{
		$selected_pages = array();
		if (!$files = scandir(_PS_ROOT_DIR_.'/controllers/front/'))
			die(Tools::displayError('Cannot scan root directory'));

		// Exclude pages forbidden
		$exlude_pages = array(
			'category', 'changecurrency', 'cms', 'footer', 'header',
			'pagination', 'product', 'product-sort', 'statistics'
		);

		foreach ($files as $file)
			if (preg_match('/^[a-z0-9_.-]*\.php$/i', $file) && !in_array(strtolower(str_replace('Controller.php', '', $file)), $exlude_pages))
				$selected_pages[] = strtolower(str_replace('Controller.php', '', $file));
		// Exclude page already filled
		if ($exclude_filled)
		{
			$metas = Meta::getMetas();
			foreach ($metas as $meta)
				if (in_array($meta['page'], $selected_pages))
					unset($selected_pages[array_search($meta['page'], $selected_pages)]);
		}
		// Add selected page
		if ($add_page)
		{
			$selected_pages[] = $add_page;
			sort($selected_pages);
		}
		return $selected_pages;
	}

	public static function getMetas()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'meta
		ORDER BY page ASC');
	}

	public static function getMetasByIdLang($id_lang, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'meta` m
				LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON m.`id_meta` = ml.`id_meta`
				WHERE ml.`id_lang` = '.(int)$id_lang
					.$shop->addSqlRestrictionOnLang('ml').
				'ORDER BY page ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	}

	public static function getMetaByPage($page, $id_lang, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT *
				FROM '._DB_PREFIX_.'meta m
				LEFT JOIN '._DB_PREFIX_.'meta_lang ml on (m.id_meta = ml.id_meta)
				WHERE m.page = \''.pSQL($page).'\'
					AND ml.id_lang = '.(int)$id_lang
					.$context->shop->addSqlRestrictionOnLang('ml');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	}

	public function update($null_values = false)
	{
		if (!parent::update($null_values))
			return false;

		return Tools::generateHtaccess();
	}

	public function delete()
	{
		if (!parent::delete())
			return false;

		return Tools::generateHtaccess();
	}

	public function deleteSelection($selection)
	{
		if (!is_array($selection))
			die(Tools::displayError());
		$result = true;
		foreach ($selection as $id)
		{
			$this->id = (int)$id;
			$result = $result && $this->delete();
		}

		return Tools::generateHtaccess();
	}

	public static function getEquivalentUrlRewrite($new_id_lang, $id_lang, $url_rewrite)
	{
		return Db::getInstance()->getValue('
		SELECT url_rewrite
		FROM `'._DB_PREFIX_.'meta_lang`
		WHERE id_meta = (
			SELECT id_meta
			FROM `'._DB_PREFIX_.'meta_lang`
			WHERE url_rewrite = \''.pSQL($url_rewrite).'\' AND id_lang = '.(int)$id_lang.'
			AND id_shop = '.Context::getContext()->shop->id.'
		)
		AND id_lang = '.(int)$new_id_lang.'
		AND id_shop = '.Context::getContext()->shop->id);
	}
}

