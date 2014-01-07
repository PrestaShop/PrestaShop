<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class NewsfeedCore extends ObjectModel
{
	/** @var string Name */
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $content;
	public $link_rewrite;
	public $id_newsfeed_category;
	public $position;
	public $indexation;
	public $active;
	public $short_content;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'newsfeed',
		'primary' => 'id_newsfeed',
		'multilang' => true,
		'fields' => array(
			'id_newsfeed_category' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'position' => 			array('type' => self::TYPE_INT),
			'indexation' =>     	array('type' => self::TYPE_BOOL),
			'active' => 			array('type' => self::TYPE_BOOL),
			'date_add' => 					array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
			'date_upd' => 					array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),

			// Lang fields
			'meta_description' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'meta_keywords' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'meta_title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
			'link_rewrite' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
			'content' => 			array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
			'short_content' => 			array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 400),
		),
	);

	protected $webserviceParameters = array(
		'objectNodeName' => 'content',
		'objectsNodeName' => 'content_management_system',
	);

	public function add($autodate = true, $null_values = false)
	{
		$this->position = Newsfeed::getLastPosition((int)$this->id_newsfeed_category);
		return parent::add($autodate, true);
	}

	public function update($null_values = false)
	{
		if (parent::update($null_values))
			return $this->cleanPositions($this->id_newsfeed_category);
		return false;
	}

	public function delete()
	{
	 	if (parent::delete())
			return $this->cleanPositions($this->id_newsfeed_category);
		return false;
	}

	public static function getLinks($id_lang, $selection = null, $active = true, Link $link = null)
	{
		if (!$link)
			$link = Context::getContext()->link;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_newsfeed, cl.link_rewrite, cl.meta_title
		FROM '._DB_PREFIX_.'newsfeed c
		LEFT JOIN '._DB_PREFIX_.'newsfeed_lang cl ON (c.id_newsfeed = cl.id_newsfeed AND cl.id_lang = '.(int)$id_lang.')
		'.Shop::addSqlAssociation('newsfeed', 'c').'
		WHERE 1
		'.(($selection !== null) ? ' AND c.id_newsfeed IN ('.implode(',', array_map('intval', $selection)).')' : '').
		($active ? ' AND c.`active` = 1 ' : '').
		'GROUP BY c.id_newsfeed
		ORDER BY c.`position`');

		$links = array();
		if ($result)
			foreach ($result as $row)
			{
				$row['link'] = $link->getNewsfeedLink((int)$row['id_newsfeed'], $row['link_rewrite']);
				$links[] = $row;
			}
		return $links;
	}

	public static function listNewsfeed($id_lang = null, $id_block = false, $active = true)
	{
		if (empty($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.id_newsfeed, l.meta_title
		FROM  '._DB_PREFIX_.'newsfeed c
		JOIN '._DB_PREFIX_.'newsfeed_lang l ON (c.id_newsfeed = l.id_newsfeed)
		'.Shop::addSqlAssociation('newsfeed', 'c').'
		'.(($id_block) ? 'JOIN '._DB_PREFIX_.'block_newsfeed b ON (c.id_newsfeed = b.id_newsfeed)' : '').'
		WHERE l.id_lang = '.(int)$id_lang.(($id_block) ? ' AND b.id_block = '.(int)$id_block : '').($active ? ' AND c.`active` = 1 ' : '').'
		GROUP BY c.id_newsfeed
		ORDER BY c.`position`');
	}

	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_newsfeed`, cp.`position`, cp.`id_newsfeed_category`
			FROM `'._DB_PREFIX_.'newsfeed` cp
			WHERE cp.`id_newsfeed_category` = '.(int)$this->id_newsfeed_category.'
			ORDER BY cp.`position` ASC'
		))
			return false;

		foreach ($res as $newsfeed)
			if ((int)$newsfeed['id_newsfeed'] == (int)$this->id)
				$moved_newsfeed = $newsfeed;

		if (!isset($moved_newsfeed) || !isset($position))
			return false;

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsfeed`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_newsfeed['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_newsfeed['position'].' AND `position` >= '.(int)$position).'
			AND `id_newsfeed_category`='.(int)$moved_newsfeed['id_newsfeed_category'])
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsfeed`
			SET `position` = '.(int)$position.'
			WHERE `id_newsfeed` = '.(int)$moved_newsfeed['id_newsfeed'].'
			AND `id_newsfeed_category`='.(int)$moved_newsfeed['id_newsfeed_category']));
	}

	public static function cleanPositions($id_category)
	{
		$sql = '
		SELECT `id_newsfeed`
		FROM `'._DB_PREFIX_.'newsfeed`
		WHERE `id_newsfeed_category` = '.(int)$id_category.'
		ORDER BY `position`';

		$result = Db::getInstance()->executeS($sql);

		for ($i = 0, $total = count($result); $i < $total; ++$i)
		{
			$sql = 'UPDATE `'._DB_PREFIX_.'newsfeed`
					SET `position` = '.(int)$i.'
					WHERE `id_newsfeed_category` = '.(int)$id_category.'
						AND `id_newsfeed` = '.(int)$result[$i]['id_newsfeed'];
			Db::getInstance()->execute($sql);
		}
		return true;
	}

	public static function getLastPosition($id_category)
	{
		$sql = '
		SELECT MAX(position) + 1
		FROM `'._DB_PREFIX_.'newsfeed`
		WHERE `id_newsfeed_category` = '.(int)$id_category;

		return (Db::getInstance()->getValue($sql));
	}

	public static function getNewsfeedPages($id_lang = null, $id_newsfeed_category = null, $active = true, $id_shop = null)
	{
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from('newsfeed', 'c');
		if ($id_lang)
			$sql->innerJoin('newsfeed_lang', 'l', 'c.id_newsfeed = l.id_newsfeed AND l.id_lang = '.(int)$id_lang);

		if ($id_shop)
			$sql->innerJoin('newsfeed_shop', 'cs', 'c.id_newsfeed = cs.id_newsfeed AND cs.id_shop = '.(int)$id_shop); 

		if ($active)
			$sql->where('c.active = 1');

		if ($id_newsfeed_category)
			$sql->where('c.id_newsfeed_category = '.(int)$id_newsfeed_category);

		$sql->orderBy('position');

		return Db::getInstance()->executeS($sql);
	}

	public static function getUrlRewriteInformations($id_newsfeed)
	{
	    $sql = 'SELECT l.`id_lang`, c.`link_rewrite`
				FROM `'._DB_PREFIX_.'newsfeed_lang` AS c
				LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
				WHERE c.`id_newsfeed` = '.(int)$id_newsfeed.'
				AND l.`active` = 1';

		return Db::getInstance()->executeS($sql);
	}
}
