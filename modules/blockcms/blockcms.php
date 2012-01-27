<?php
/*
 * 2007-2011 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @version  Release: $Revision: 7060 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
	exit;

class BlockCms extends Module
{
	private $_html;

	const LEFT_COLUMN = 0;
	const RIGHT_COLUMN = 1;

	public function __construct()
	{
		$this->name = 'blockcms';
		$this->tab = 'front_office_features';
		$this->version = 1.1;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('CMS Block');
		$this->description = $this->l('Adds a block with several CMS links.');
		$this->secure_key = Tools::encrypt($this->name);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('leftColumn') || !$this->registerHook('rightColumn') || !$this->registerHook('footer') || !$this->registerHook('header') ||
		!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block`(
		`id_cms_block` int(10) unsigned NOT NULL auto_increment,
		`id_cms_category` int(10) unsigned NOT NULL,
		`location` tinyint(1) unsigned NOT NULL,
		`position` int(10) unsigned NOT NULL default \'0\',
		`display_store` tinyint(1) unsigned NOT NULL default \'1\',
		PRIMARY KEY (`id_cms_block`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8') ||
		!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block_lang`(
		`id_cms_block` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`name` varchar(40) NOT NULL default \'\',
		PRIMARY KEY (`id_cms_block`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8') ||
		!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block_page`(
		`id_cms_block_page` int(10) unsigned NOT NULL auto_increment,
		`id_cms_block` int(10) unsigned NOT NULL,
		`id_cms` int(10) unsigned NOT NULL,
		`is_category` tinyint(1) unsigned NOT NULL,
		PRIMARY KEY (`id_cms_block_page`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8') ||
		!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block_shop` (
		`id_cms_block` int(10) unsigned NOT NULL auto_increment,
		`id_shop` int(10) unsigned NOT NULL,
		PRIMARY KEY (`id_cms_block`, `id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8') ||
		!Configuration::updateValue('FOOTER_CMS', '') ||
		!Configuration::updateValue('FOOTER_BLOCK_ACTIVATION', 1) ||
		!Configuration::updateValue('FOOTER_POWEREDBY', 1))
		return false;

		// Install fixtures for blockcms
		if (!Db::getInstance()->insert('cms_block', array(
			'id_cms_category' =>	1,
			'location' =>			0,
			'position' =>			0,
		)))
			return false;
		$id_cms_block = Db::getInstance()->Insert_ID();
		$result = true;
		$shops = Shop::getShops(true, null, true);
		foreach ($shops as $shop)
			$result &= Db::getInstance()->insert('cms_block_shop', array(
				'id_cms_block' =>	$id_cms_block,
				'id_shop' =>		$shop
			));
			

		foreach (Language::getLanguages(false) as $lang)
			$result &= Db::getInstance()->insert('cms_block_lang', array(
				'id_cms_block' =>	$id_cms_block,
				'id_lang' =>		$lang['id_lang'],
				'name' =>			$this->l('Information'),
			));

		foreach (CMS::getCMSPages(null, 1) as $cms)
			$result &= Db::getInstance()->insert('cms_block_page', array(
				'id_cms_block' =>	$id_cms_block,
				'id_cms' =>			$cms['id_cms'],
				'is_category' =>	0,
			));

		return $result;
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
		!Configuration::deleteByName('FOOTER_CMS') ||
		!Configuration::deleteByName('FOOTER_BLOCK_ACTIVATION') ||
		!Configuration::deleteByName('FOOTER_POWEREDBY') ||
		!Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'cms_block` , `'._DB_PREFIX_.'cms_block_page`, `'._DB_PREFIX_.'cms_block_lang`, `'._DB_PREFIX_.'cms_block_shop`'))
		return false;
		return true;
	}

	public function getBlockCMS($id_cms_block)
	{
		$cmsBlocks = Db::getInstance()->executeS('
		SELECT cb.`id_cms_category`, cb.`location`, cb.`display_store`, cbl.id_lang, cbl.name
		FROM `'._DB_PREFIX_.'cms_block` cb
		LEFT JOIN `'._DB_PREFIX_.'cms_block_lang` cbl ON (cbl.`id_cms_block` = cb.`id_cms_block`)
		WHERE cb.`id_cms_block` = '.(int)$id_cms_block);

		$store_display_update = array(0, $size = count($cmsBlocks), $display = Configuration::get('PS_STORES_DISPLAY_FOOTER'));
		foreach ($cmsBlocks as $cmsBlock)
		{
			$cmsBlocks['name'][(int)$cmsBlock['id_lang']] = $cmsBlock['name'];
			if ($store_display_update['0'] < $store_display_update['1'])
			$cmsBlocks[$store_display_update['0']]['display_store'] = $store_display_update['2'];
			++$store_display_update['0'];
		}
		return $cmsBlocks;
	}

	private function getBlocksCMS($location)
	{
		return Db::getInstance()->executeS('
		SELECT bc.`id_cms_block`, bcl.`name` block_name, ccl.`name` category_name, bc.`position`, bc.`id_cms_category`, bc.`display_store`
		FROM `'._DB_PREFIX_.'cms_block` bc
		INNER JOIN `'._DB_PREFIX_.'cms_category_lang` ccl ON (bc.`id_cms_category` = ccl.`id_cms_category`)
		INNER JOIN `'._DB_PREFIX_.'cms_block_lang` bcl ON (bc.`id_cms_block` = bcl.`id_cms_block`)
		WHERE ccl.`id_lang` = '.(int)$this->context->language->id.' AND bc.`location` = '.(int)$location.' AND bcl.`id_lang` = '.(int)$this->context->language->id.'
		ORDER BY bc.`position`');
	}

	public function getAllBlocksCMS()
	{
		return array_merge($this->getBlocksCMS(self::LEFT_COLUMN), $this->getBlocksCMS(self::RIGHT_COLUMN));
	}

	public static function getCMStitlesFooter()
	{
		$context = Context::getContext();

		$footerCms = Configuration::get('FOOTER_CMS');
		if (empty($footerCms))
		return array();
		$cmsCategories = explode('|', $footerCms);
		$content = array();

		foreach ($cmsCategories as $cmsCategory)
		{
			$ids = explode('_', $cmsCategory);
			if ($ids[0] == 1)
			{
				$query = Db::getInstance()->getRow('
				SELECT cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category_lang` cl
				INNER JOIN `'._DB_PREFIX_.'cms_category` c ON (cl.`id_cms_category` = c.`id_cms_category`)
				WHERE cl.`id_cms_category` = '.(int)$ids[1].' AND (c.`active` = 1 OR c.`id_cms_category` = 1)
				AND cl.`id_lang` = '.(int)$context->language->id);

				$content[$cmsCategory]['link'] = $context->link->getCMSCategoryLink((int)$ids[1], $query['link_rewrite']);
				$content[$cmsCategory]['meta_title'] = $query['name'];
			}
			elseif (!$ids[0])
			{
				$query = Db::getInstance()->getRow('
				SELECT cl.`meta_title`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_lang` cl
				INNER JOIN `'._DB_PREFIX_.'cms` c ON (cl.`id_cms` = c.`id_cms`)
				WHERE cl.`id_cms` = '.(int)$ids[1].' AND c.`active` = 1
				AND cl.`id_lang` = '.(int)$context->language->id);

				$content[$cmsCategory]['link'] = $context->link->getCMSLink((int)$ids[1], $query['link_rewrite']);
				$content[$cmsCategory]['meta_title'] = $query['meta_title'];
			}
		}

		return $content;
	}

	public static function getCMStitles($location)
	{
		$context = Context::getContext();

		$cmsCategories = Db::getInstance()->executeS('
		SELECT bc.`id_cms_block`, bc.`id_cms_category`, bc.`display_store`, ccl.`link_rewrite`, ccl.`name` category_name, bcl.`name` block_name
		FROM `'._DB_PREFIX_.'cms_block` bc
		LEFT JOIN `'._DB_PREFIX_.'cms_block_shop` bcs ON (bcs.id_cms_block = bc.id_cms_block)
		INNER JOIN `'._DB_PREFIX_.'cms_category_lang` ccl ON (bc.`id_cms_category` = ccl.`id_cms_category`)
		INNER JOIN `'._DB_PREFIX_.'cms_block_lang` bcl ON (bc.`id_cms_block` = bcl.`id_cms_block`)
		WHERE bc.`location` = '.(int)($location).' AND ccl.`id_lang` = '.(int)$context->language->id.' AND bcl.`id_lang` = '.(int)$context->language->id.' AND bcs.id_shop = '.$context->shop->getID(true).'
		ORDER BY `position`');

		$content = array();

		if (is_array($cmsCategories) && count($cmsCategories))
		foreach ($cmsCategories as $cmsCategory)
		{
			$key = (int)$cmsCategory['id_cms_block'];
			$content[$key]['display_store'] = $cmsCategory['display_store'];

			$content[$key]['cms'] = Db::getInstance()->executeS('
				SELECT cl.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_block_page` bcp
				INNER JOIN `'._DB_PREFIX_.'cms_lang` cl ON (bcp.`id_cms` = cl.`id_cms`)
				INNER JOIN `'._DB_PREFIX_.'cms` c ON (bcp.`id_cms` = c.`id_cms`)
				WHERE bcp.`id_cms_block` = '.(int)$cmsCategory['id_cms_block'].' AND cl.`id_lang` = '.(int)$context->language->id.' AND bcp.`is_category` = 0 AND c.`active` = 1
				ORDER BY `position`
			');

			$links = array();
			if (count($content[$key]['cms']))
				foreach ($content[$key]['cms'] as $row)
				{
					$row['link'] = $context->link->getCMSLink((int)($row['id_cms']), $row['link_rewrite']);
					$links[] = $row;
				}

			$content[$key]['cms'] = $links;

			$content[$key]['categories'] = Db::getInstance()->executeS('
				SELECT bcp.`id_cms`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_block_page` bcp
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl ON (bcp.`id_cms` = cl.`id_cms_category`)
				WHERE bcp.`id_cms_block` = '.(int)$cmsCategory['id_cms_block'].'
				AND cl.`id_lang` = '.(int)$context->language->id.'
				AND bcp.`is_category` = 1');

			$links = array();
			if (count($content[$key]['categories']))
				foreach ($content[$key]['categories'] as $row)
				{
					$row['link'] = $context->link->getCMSCategoryLink((int)$row['id_cms'], $row['link_rewrite']);
					$links[] = $row;
				}

			$content[$key]['categories'] = $links;
			$content[$key]['name'] = $cmsCategory['block_name'];
			$content[$key]['category_link'] = $context->link->getCMSCategoryLink((int)$cmsCategory['id_cms_category'], $cmsCategory['link_rewrite']);
			$content[$key]['category_name'] = $cmsCategory['category_name'];
		}

		return $content;
	}

	public function getAllCMSTitles()
	{
		$titles = array();
		foreach (self::getCMStitles(self::LEFT_COLUMN) as $key => $title)
		{
			unset($title['categories'], $title['name'], $title['category_link'], $title['category_name']);
			$titles[$key] = $title;
		}
		foreach (self::getCMStitles(self::RIGHT_COLUMN) as $key => $title)
		{
			unset($title['categories'], $title['name'], $title['category_link'], $title['category_name']);
			$titles[$key] = $title;
		}
		return $titles;
	}

	private function displayRecurseCheckboxes($categories, $selected, $has_suite = array())
	{
		static $irow = 0;

		$img = $categories['level_depth'] == 0 ? 'lv1.gif' : 'lv'.($categories['level_depth'] + 1).'_'.((count($categories['cms']) || isset($categories['children'])) ? 'b' : 'f').'.gif';

		$this->_html .= '
			<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
				<td width="3%"><input type="checkbox" name="footerBox[]" class="cmsBox" id="1_'.$categories['id_cms_category'].'" value="1_'.$categories['id_cms_category'].'" '.
		(in_array('1_'.$categories['id_cms_category'], $selected) ? ' checked="checked"' : '').' /></td>
				<td width="3%">'.$categories['id_cms_category'].'</td>
				<td width="94%">';
		for ($i = 1; $i < $categories['level_depth']; $i++)
		$this->_html .=	'<img style="vertical-align:middle;" src="../img/admin/lvl_'.$has_suite[$i - 1].'.gif" alt="" />';
		$this->_html .= '<img style="vertical-align:middle;" src="../img/admin/'.($categories['level_depth'] == 0 ? 'lv1' : 'lv2_'.(($has_suite[$categories['level_depth'] - 1]) ? 'b' : 'f')).'.gif" alt="" /> &nbsp;
				<label for="1_'.$categories['id_cms_category'].'" class="t"><b>'.$categories['name'].'</b></label></td>
			</tr>';
		if (isset($categories['children']))
		foreach ($categories['children'] as $key => $category)
		{
			$has_suite[$categories['level_depth']] = 1;
			if (count($categories['children']) == $key + 1 && !count($categories['cms']))
			$has_suite[$categories['level_depth']] = 0;
			$this->displayRecurseCheckboxes($category, $selected, $has_suite, 0);
		}

		$cpt = 0;
		foreach ($categories['cms'] as $cms)
		{
			$this->_html .= '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
					<td width="3%"><input type="checkbox" name="footerBox[]" class="cmsBox" id="0_'.$cms['id_cms'].'" value="0_'.$cms['id_cms'].'" '.
			(in_array('0_'.$cms['id_cms'], $selected) ? ' checked="checked"' : '').' /></td>
					<td width="3%">'.$cms['id_cms'].'</td>
					<td width="94%">';
			for ($i = 0; $i < $categories['level_depth']; $i++)
			$this->_html .=	'<img style="vertical-align:middle;" src="../img/admin/lvl_'.$has_suite[$i].'.gif" alt="" />';
			$this->_html .= '<img style="vertical-align:middle;" src="../img/admin/lv2_'.(++$cpt == count($categories['cms']) ? 'f' : 'b').'.gif" alt="" /> &nbsp;
			<label for="0_'.$cms['id_cms'].'" class="t" style="margin-top:6px;">'.$cms['meta_title'].'</label></td>
				</tr>';
		}
	}

	private function _displayForm()
	{
		$this->context = Context::getContext();
		$cms_blocks_left = $this->getBlocksCMS(0);
		$cms_blocks_right = $this->getBlocksCMS(1);

		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$this->context->language->iso_code.'.js') ? $this->context->language->iso_code : 'en');
		$ad = dirname($_SERVER['PHP_SELF']);

		$this->_html .= '
		<script type="text/javascript">
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
		<script type="text/javascript" src="../modules/blockcms/blockcms.js"></script>
		<script type="text/javascript">CMSBlocksDnD(\''.$this->secure_key.'\');</script>
		<fieldset>
			<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('CMS block configuration').'</legend>

			<p><a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addBlockCMS"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('Add a new CMS block').'</a></p>';

		$this->_html .= '<div style="width:440px; float:left; margin-right:10px;" ><h3>'.$this->l('List of Left CMS blocks').'</h3>';
		if (count($cms_blocks_left))
		{
			$this->_html .= '<table width="100%" class="table" cellspacing="0" cellpadding="0" id="table_left" class="tableDnD">
			<thead>
			<tr class="nodrag nodrop">
				<th width="10%"><b>'.$this->l('ID').'</b></th>
				<th width="30%" class="center"><b>'.$this->l('Name of block').'</b></th>
				<th width="30%" class="center"><b>'.$this->l('Category Name').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Position').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Actions').'</b></th>
			</tr>
			</thead>
			<tbody>
			';
			$irow = 0;
			foreach ($cms_blocks_left as $cms_block)
			{
				$this->_html .= '
					<tr id="tr_0_'.$cms_block['id_cms_block'].'_'.$cms_block['position'].'" '.($irow++ % 2 ? 'class="alt_row"' : '').'>
						<td width="10%">'.$cms_block['id_cms_block'].'</td>
						<td width="30%" class="center">'.(empty($cms_block['block_name']) ? $cms_block['category_name'] : $cms_block['block_name']).'</td>
						<td width="30%" class="center">'.$cms_block['category_name'].'</td>
						<td class="center pointer dragHandle">
							<a'.(($cms_block['position'] == (count($cms_blocks_left) - 1) || count($cms_blocks_left) == 1) ? ' style="display: none;"' : '').' href="'.AdminController::$currentIndex.'&configure=blockcms&id_cms_block='.$cms_block['id_cms_block'].'&way=1&position='.(int)($cms_block['position'] + 1).'&location=0&token='.Tools::getAdminTokenLite('AdminModules').'">
							<img src="../img/admin/down.gif" alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>
							<a'.($cms_block['position'] == 0 ? ' style="display: none;"' : '').' href="'.AdminController::$currentIndex.'&configure=blockcms&id_cms_block='.$cms_block['id_cms_block'].'&way=0&position='.(int)($cms_block['position'] - 1).'&location=0&token='.Tools::getAdminTokenLite('AdminModules').'">
							<img src="../img/admin/up.gif" alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>
						</td>
						<td width="10%" class="center">
							<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlockCMS&id_cms_block='.(int)($cms_block['id_cms_block']).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a>
							<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockCMS&id_cms_block='.(int)($cms_block['id_cms_block']).'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>
						</td>
					</tr>';
			}
			$this->_html .= '
			</tbody>
			</table>';
		}
		else
		$this->_html .= '<p style="margin-left:40px;">'.$this->l('There is no CMS block set').'</p>';
		$this->_html .= '</div>';

		$this->_html .= '<div style="width:440px; float:left;" ><h3>'.$this->l('List of Right CMS blocks').'</h3>';
		if (count($cms_blocks_right))
		{
			$this->_html .= '<table width="100%" class="table" cellspacing="0" cellpadding="0" id="table_right" class="tableDnD">
			<thead>
			<tr class="nodrag nodrop">
				<th width="10%"><b>'.$this->l('ID').'</b></th>
				<th width="30%" class="center"><b>'.$this->l('Name of block').'</b></th>
				<th width="30%" class="center"><b>'.$this->l('Category Name').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Position').'</b></th>
				<th width="10%" class="center"><b>'.$this->l('Actions').'</b></th>
			</tr>
			</thead>
			<tbody>
			';
			$irow = 0;
			foreach ($cms_blocks_right as $cms_block)
			{
				$this->_html .= '
					<tr id="tr_1_'.$cms_block['id_cms_block'].'_'.$cms_block['position'].'" '.($irow++ % 2 ? 'class="alt_row"' : '').'>
						<td width="10%">'.$cms_block['id_cms_block'].'</td>
						<td width="30%" class="center">'.(empty($cms_block['block_name']) ? $cms_block['category_name'] : $cms_block['block_name']).'</td>
						<td width="30%" class="center">'.$cms_block['category_name'].'</td>
						<td class="center pointer dragHandle">
							<a'.(($cms_block['position'] == (count($cms_blocks_right) - 1) || count($cms_blocks_right) == 1) ? ' style="display: none;"' : '').' href="'.AdminController::$currentIndex.'&configure=blockcms&id_cms_block='.$cms_block['id_cms_block'].'&way=1&position='.(int)($cms_block['position'] + 1).'&location=1&token='.Tools::getAdminTokenLite('AdminModules').'">
							<img src="../img/admin/down.gif" alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>
							<a'.($cms_block['position'] == 0 ? ' style="display: none;"' : '').' href="'.AdminController::$currentIndex.'&configure=blockcms&id_cms_block='.$cms_block['id_cms_block'].'&way=0&position='.(int)($cms_block['position'] - 1).'&location=1&token='.Tools::getAdminTokenLite('AdminModules').'">
							<img src="../img/admin/up.gif" alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>
						</td>
						<td width="10%" class="center">
							<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlockCMS&id_cms_block='.(int)($cms_block['id_cms_block']).'" title="'.$this->l('Edit').'"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" /></a>
							<a href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockCMS&id_cms_block='.(int)($cms_block['id_cms_block']).'" title="'.$this->l('Delete').'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" /></a>
						</td>
					</tr>';
			}
			$this->_html .= '
			</tbody>
			</table>';
		}
		else
		$this->_html .= '<p style="margin-left:40px;">'.$this->l('There is no CMS block set').'</p>';

		$languages = Language::getLanguages(false);
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$div_id_language = 'block_language';

		$footer_content_i18n = '<script type="text/javascript">id_language = Number('.$default_language.');</script>';
		foreach ($languages as $lang)
			$footer_content_i18n .= '<div id="block_language_'.$lang['id_lang'].'" style="display: '.($lang['id_lang'] == $default_language ? 'block' : 'none').'; float:left;">
										<textarea class="rte" name="footer_text_'.(int)$lang['id_lang'].'">'.Tools::htmlentitiesUTF8(Configuration::get('FOOTER_CMS_TEXT_'.(int)$lang['id_lang'])).'</textarea>
									</div>';

		$footer_content_i18n .= $this->displayFlags($languages, $default_language, $div_id_language, $div_id_language, true);

		$this->_html .= '</div>
			<div class="clear"></div>
		</fieldset><br />
		<form method="POST" action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'">
		<fieldset>
			<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Footer\'s various links Configuration').'</legend>
			<input type="checkbox" name="footer_poweredby_active" id="footer_poweredby_active" '.(((int)Configuration::get('FOOTER_POWEREDBY') === 1 || Configuration::get('FOOTER_POWEREDBY') === false) ? 'checked="checked"' : '').'> <label for="footer_active" style="float:none;">'.$this->l('Display "Powered by Prestashop"').'</label><br /><br />
			<input type="checkbox" name="footer_active" id="footer_active" '.(Configuration::get('FOOTER_BLOCK_ACTIVATION') ? 'checked="checked"' : '').'> <label for="footer_active" style="float:none;">'.$this->l('Display the Footer\'s various links').'</label><br /><br />
			<label style="display:block; padding:0; text-align: left">'.$this->l('Footer informations:').'</label><br /><br />
			'.$footer_content_i18n.'

			<table cellspacing="0" style="margin-top: 20px" cellpadding="0" class="table" width="100%">
				<tr>
					<th width="3%"><input type="checkbox" name="checkme" class="noborder" onclick="checkallCMSBoxes($(this).attr(\'checked\'))" /></th>
					<th width="3%">'.$this->l('ID').'</th>
					<th width="94%">'.$this->l('Name').'</th>
				</tr>';
		$this->displayRecurseCheckboxes(CMSCategory::getRecurseCategory($this->context->language->id), explode('|', Configuration::get('FOOTER_CMS')));
		$this->_html .= '
			</table>
			<p class="center"><input type="submit" class="button" name="submitFooterCMS" value="'.$this->l('Save').'" /></p>
		</fieldset>
		</form>';
	}

	private function _displayAddForm()
	{
		$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$divLangName = 'name';

		$cmsBlock = null;
		if (Tools::isSubmit('editBlockCMS') && Tools::getValue('id_cms_block'))
		$cmsBlock = $this->getBlockCMS((int)Tools::getValue('id_cms_block'));

		$this->_html .= '
		<script type="text/javascript" src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/'.$this->name.'.js"></script>
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
		<form method="POST" action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'">
		';
		if (Tools::getValue('id_cms_block'))
		$this->_html .= '<input type="hidden" name="id_cms_block" value="'.(int)Tools::getValue('id_cms_block').'" id="id_cms_block" />';
		$this->_html .= '
		<fieldset>';

		if (Tools::isSubmit('addBlockCMS'))
		$this->_html .= '<legend><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('New CMS block').'</legend>';
		elseif (Tools::isSubmit('editBlockCMS'))
		$this->_html .= '<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Edit CMS block').'</legend>';

		$this->_html .= '
			<label>'.$this->l('Name of block:').'</label>
			<div class="margin-form">';

		foreach ($languages as $language)
		$this->_html .= '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="block_name_'.$language['id_lang'].'" id="block_name_'.$language['id_lang'].'" size="30" value="'.(Tools::getValue('block_name_'.$language['id_lang']) ? Tools::getValue('block_name_'.$language['id_lang']) : (isset($cmsBlock['name'][$language['id_lang']]) ? $cmsBlock['name'][$language['id_lang']] : '')).'" />
					</div>';
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'name', true);
		$this->_html .= '<p class="clear">'.$this->l('If you leave this field empty, the block name will use the category name').'</p>
			</div><br />
			<label for="id_category">'.$this->l('Choose a CMS category:').'</label>
			<div class="margin-form">
				<select name="id_category" id="id_category" onchange="CMSCategory_js($(this).val(), \''.$this->secure_key.'\')">';
		$categories = CMSCategory::getCategories($this->context->language->id, false);
		$this->_html .= CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, ($cmsBlock != null ? $cmsBlock[0]['id_cms_category'] : 1), 1);
		$this->_html .= '
				</select>
			</div><br />

			<label>'.$this->l('Location:').'</label>
			<div class="margin-form">
				<select name="block_location" id="block_location">
					<option value="'.self::LEFT_COLUMN.'" '.(($cmsBlock && $cmsBlock[0]['location'] == self::LEFT_COLUMN) ? 'selected="selected"' : '').'>'.$this->l('Left').'</option>
					<option value="'.self::RIGHT_COLUMN.'" '.(($cmsBlock && $cmsBlock[0]['location'] == self::RIGHT_COLUMN) ? 'selected="selected"' : '').'>'.$this->l('Right').'</option>
				</select>
			</div>';
		$this->_html .=	'
			<label for="PS_STORES_DISPLAY_CMS_on">'.$this->l('Display Stores:').'</label>
			<div class="margin-form">
				<img src="../img/admin/enabled.gif" alt="Yes" title="Yes" />
		        <input type="radio" name="PS_STORES_DISPLAY_CMS" id="PS_STORES_DISPLAY_CMS_on" '.(($cmsBlock && (isset($cmsBlock[0]['display_store']) && $cmsBlock[0]['display_store'] == 0)) ? '' : 'checked="checked" ').'value="1" />
			    <label class="t" for="PS_STORES_DISPLAY_CMS_on">'.$this->l('Yes').'</label>
			    <img src="../img/admin/disabled.gif" alt="No" title="No" style="margin-left: 10px;" />
			    <input type="radio" name="PS_STORES_DISPLAY_CMS" id="PS_STORES_DISPLAY_CMS_off" '.(($cmsBlock && (isset($cmsBlock[0]['display_store']) && $cmsBlock[0]['display_store'] == 0)) ? 'checked="checked" ' : '').'value="0" />
			    <label  class="t" for="PS_STORES_DISPLAY_CMS_off">'.$this->l('No').'</label><br />'
			    .$this->l('Display "our stores" at the end of the block')
			    .'</div>';
			    $this->_html .=	'<div class="margin-form" id="cms_subcategories"></div><div class="clear">&nbsp;</div>';
			
			$helper = new Helper();
			$helper->id = (int)Tools::getValue('id_cms_block');
			$helper->table = 'cms_block';
			$helper->identifier = 'id_cms_block';
			
			if (Shop::isFeatureActive())
				$this->_html .= '<label for="shop_association">'.$this->l('Shop association:').'</label><div id="shop_association" class="margin-form">'.$helper->renderAssoShop().'</div>';
	    $this->_html .= '
			<p class="center">
				<input type="submit" class="button" name="submitBlockCMS" value="'.$this->l('Save').'" />
				<a class="button" style="position:relative; padding:3px 3px 4px 3px; top:1px" href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">'.$this->l('Cancel').'</a>
			</p>
			</fieldset>
			</form>
			<script type="text/javascript">CMSCategory_js($(\'#id_category\').val(), \''.$this->secure_key.'\')</script>';
	}

	private function _postValidation()
	{
		$errors = array();
		if (Tools::isSubmit('submitBlockCMS'))
		{
			$languages = Language::getLanguages(false);
			$cmsBoxes = Tools::getValue('cmsBox');
			if (!Validate::isInt(Tools::getValue('PS_STORES_DISPLAY_CMS')) || (Tools::getValue('PS_STORES_DISPLAY_CMS') != 0 && Tools::getValue('PS_STORES_DISPLAY_CMS') != 1))
				$errors[] = $this->l('Invalid store display');
			if (!Validate::isInt(Tools::getValue('block_location')) || (Tools::getValue('block_location') != self::LEFT_COLUMN && Tools::getValue('block_location') != self::RIGHT_COLUMN))
				$errors[] = $this->l('Invalid block location');
			if (!is_array($cmsBoxes))
				$errors[] = $this->l('You must choose at least one page or subcategory to create a CMS block.');
			else
				foreach ($cmsBoxes as $cmsBox)
					if (!preg_match('#^[01]_[0-9]+$#', $cmsBox))
						$errors[] = $this->l('Invalid CMS page or category');
				foreach ($languages as $language)
					if (strlen(Tools::getValue('block_name_'.$language['id_lang'])) > 40)
						$errors[] = $this->l('Block name is too long');
		}
		elseif (Tools::isSubmit('deleteBlockCMS') && !Validate::isInt(Tools::getValue('id_cms_block')))
			$errors[] = $this->l('Invalid id_cms_block');
		elseif (Tools::isSubmit('submitFooterCMS'))
		{
			if (Tools::getValue('footerBox'))
			foreach (Tools::getValue('footerBox') as $cmsBox)
			if (!preg_match('#^[01]_[0-9]+$#', $cmsBox))
			$errors[] = $this->l('Invalid CMS page or category');

			$empty_footer_text = true;
			$footer_text = array();
			$footer_text[(int)Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('footer_text_'.(int)Configuration::get('PS_LANG_DEFAULT'));

			$languages = Language::getLanguages(false);
			// set default values
			foreach ($languages as $lang)
			{
				if ($lang['id_lang'] == (int)Configuration::get('PS_LANG_DEFAULT'))
					continue;

				$footer_text_value = Tools::getValue('footer_text_'.(int)$lang['id_lang']);
				if (!empty($footer_text_value))
				{
					$empty_footer_text = false;
					$footer_text[(int)$lang['id_lang']] = $footer_text_value;
				}
				else
					$footer_text[(int)$lang['id_lang']] = $footer_text[(int)Configuration::get('PS_LANG_DEFAULT')];
			}

			if (!$empty_footer_text && empty($footer_text[(int)Configuration::get('PS_LANG_DEFAULT')]))
				$errors[] = $this->l('Please provide a footer text for the default language');
			else
			{
				foreach ($languages as $lang)
					Configuration::updateValue('FOOTER_CMS_TEXT_'.(int)$lang['id_lang'], $footer_text[(int)$lang['id_lang']], true);
			}

			if (Tools::getValue('footer_active') != 0 && Tools::getValue('footer_active') != 1)
			$errors[] = $this->l('Invalid activation footer');
		}
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			return false;
		}
		return true;
	}

	private function changePosition()
	{
		if (!Validate::isInt(Tools::getValue('position')) ||
		(Tools::getValue('location') != self::LEFT_COLUMN && Tools::getValue('location') != self::RIGHT_COLUMN) ||
		(Tools::getValue('way') != 0 && Tools::getValue('way') != 1))
		Tools::displayError();

		$this->_html .= 'pos change!';
		if (Tools::getValue('way') == 0)
		{
			if (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'cms_block`
			SET `position` = '.((int)Tools::getValue('position') + 1).'
			WHERE `position` = '.((int)Tools::getValue('position')).'
			AND `location` = '.(int)Tools::getValue('location')))
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cms_block`
				SET `position` = '.((int)Tools::getValue('position')).'
				WHERE `id_cms_block` = '.(int)Tools::getValue('id_cms_block'));
		}
		elseif (Tools::getValue('way') == 1)
		{
			if (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'cms_block`
			SET `position` = '.((int)Tools::getValue('position') - 1).'
			WHERE `position` = '.((int)Tools::getValue('position')).'
			AND `location` = '.(int)Tools::getValue('location')))
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cms_block`
				SET `position` = '.((int)Tools::getValue('position')).'
				WHERE `id_cms_block` = '.(int)Tools::getValue('id_cms_block'));
		}
		Tools::redirectAdmin(AdminController::$currentIndex.'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('submitBlockCMS'))
		{
			$position = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'cms_block`
			WHERE `location` = '.(int)Tools::getValue('block_location'));
			$languages = Language::getLanguages(false);
			if (Tools::isSubmit('addBlockCMS'))
			{
				Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'cms_block` (`id_cms_category`, `location`, `position`, `display_store`)
				VALUES('.(int)Tools::getValue('id_category').', '.(int)Tools::getValue('block_location').',
				'.(int)$position.', '.(int)Tools::getValue('PS_STORES_DISPLAY_CMS').')');
				$id_cms_block = Db::getInstance()->Insert_ID();
				foreach ($languages as $language)
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'cms_block_lang` (`id_cms_block`, `id_lang`, `name`)
					VALUES('.(int)$id_cms_block.', '.(int)$language['id_lang'].',
					"'.pSQL(Tools::getValue('block_name_'.$language['id_lang'])).'")');

				Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cms_block`
				SET `display_store` = '.Configuration::get('PS_STORES_DISPLAY_FOOTER'));
			}
			elseif (Tools::isSubmit('editBlockCMS'))
			{
				$id_cms_block = Tools::getvalue('id_cms_block');

				$old_block = Db::getInstance()->executeS('
				SELECT `location`, `position`
				FROM `'._DB_PREFIX_.'cms_block`
				WHERE `id_cms_block` = '.(int)$id_cms_block);

				$location_change = ($old_block[0]['location'] != (int)Tools::getvalue('block_location'));
				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'cms_block_page`
				WHERE `id_cms_block` = '.(int)$id_cms_block);

				if ($location_change == true)
				Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'cms_block`
					SET `position` = (`position` - 1) WHERE `position` > '.(int)$old_block[0]['position'].'
					AND `location` = '.(int)$old_block[0]['location']);

				Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cms_block`
				SET `location` = '.(int)(Tools::getvalue('block_location')).',
				`id_cms_category` = '.(int)(Tools::getvalue('id_category')).'
				'.($location_change == true ? ',
				`position` = '.(int)($position) : '').',
				`display_store` = '.(int)(Tools::getValue('PS_STORES_DISPLAY_CMS')).'
				WHERE `id_cms_block` = '.(int)($id_cms_block));

				Configuration::updateValue('PS_STORES_DISPLAY_FOOTER', (int)(Tools::getValue('PS_STORES_DISPLAY_CMS')));

				foreach ($languages as $language)
				Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'cms_block_lang`
					SET `name` = "'.pSQL(Tools::getValue('block_name_'.$language['id_lang'])).'"
					WHERE `id_cms_block` = '.(int)$id_cms_block.'
					AND `id_lang`= '.(int)$language['id_lang']);
			}
			
			if (Tools::isSubmit('submitBlockCMS') || Tools::isSubmit('editBlockCMS'))
			{
				$assos_shop = Tools::getValue('checkBoxShopAsso_cms_block');
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cms_block_shop WHERE id_cms_block='.(int)$id_cms_block);
				foreach ($assos_shop as $asso)
					foreach ($asso as $id_shop => $row)
						Db::getInstance()->insert('cms_block_shop', array(
							'id_cms_block' =>	(int)$id_cms_block,
							'id_shop' => (int)$id_shop,
						));
			}
			
			$cmsBoxes = Tools::getValue('cmsBox');
			if (count($cmsBoxes))
			foreach ($cmsBoxes as $cmsBox)
			{
				$cms_properties = explode('_', $cmsBox);
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'cms_block_page` (`id_cms_block`, `id_cms`, `is_category`)
					VALUES('.(int)$id_cms_block.', '.(int)$cms_properties[1].', '.(int)$cms_properties[0].')');
			}

			if (Tools::isSubmit('addBlockCMS'))
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addBlockCMSConfirmation');
			elseif (Tools::isSubmit('editBlockCMS'))
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlockCMSConfirmation');
		}
		elseif (Tools::isSubmit('deleteBlockCMS') && Tools::getValue('id_cms_block'))
		{
			$old_block = Db::getInstance()->executeS('SELECT `location`, `position` FROM `'._DB_PREFIX_.'cms_block` WHERE `id_cms_block` = '.Tools::getvalue('id_cms_block'));
			if (count($old_block))
			{
				Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cms_block`
				SET `position` = (`position` - 1)
				WHERE `position` > '.(int)$old_block[0]['position'].'
				AND `location` = '.(int)$old_block[0]['location']);

				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'cms_block`
				WHERE `id_cms_block` = '.(int)(Tools::getValue('id_cms_block')));

				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'cms_block_page`
				WHERE `id_cms_block` = '.(int)(Tools::getValue('id_cms_block')));

				Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockCMSConfirmation');
			}
			else
			$this->_html .= $this->displayError($this->l('Error: you are trying to delete a non-existent block cms'));
		}
		elseif (Tools::isSubmit('submitFooterCMS'))
		{
			$footer = '';
			if (Tools::getValue('footerBox'))
			foreach (Tools::getValue('footerBox') as $box)
			$footer .= $box.'|';
			Configuration::updateValue('FOOTER_CMS', rtrim($footer, '|'));
			Configuration::updateValue('FOOTER_BLOCK_ACTIVATION', Tools::getValue('footer_active'));
			Configuration::updateValue('FOOTER_POWEREDBY', (Tools::getValue('footer_poweredby_active') == 'on' ? 1 : 0));



			$this->_html = $this->displayConfirmation($this->l('Footer\'s CMS updated'));
		}
		elseif (Tools::isSubmit('addBlockCMSConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Block CMS added'));
		elseif (Tools::isSubmit('editBlockCMSConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Block CMS edited'));
		elseif (Tools::isSubmit('deleteBlockCMSConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
		elseif (Tools::isSubmit('id_cms_block') && Tools::isSubmit('way') && Tools::isSubmit('position') && Tools::isSubmit('location'))
			$this->changePosition();
	}

	public function getContent()
	{
		$this->_html = '';
		if ($this->_postValidation())
		$this->_postProcess();
		$this->_html .= '<h2>'.$this->l('CMS Block configuration').'</h2>';
		if (Tools::isSubmit('addBlockCMS') || Tools::isSubmit('editBlockCMS'))
		$this->_displayAddForm();
		else
		$this->_displayForm();
		return $this->_html;
	}

	public function displayBlockCMS($column)
	{
		$cms_titles = self::getCMStitles($column);
		$this->smarty->assign(array(
			'block' => 1,
			'cms_titles' => $cms_titles,
			'contact_url' => (_PS_VERSION_ >= 1.5) ? 'contact' : 'contact-form'
		));
		return $this->display(__FILE__, 'blockcms.tpl');
	}

	public function hookLeftColumn()
	{
		return $this->displayBlockCMS(self::LEFT_COLUMN);
	}

	public function hookRightColumn()
	{
		return $this->displayBlockCMS(self::RIGHT_COLUMN);
	}

	public function hookFooter()
	{
		if (Configuration::get('FOOTER_BLOCK_ACTIVATION'))
		{
			$cms_titles = self::getCMStitlesFooter();
			$this->smarty->assign(array(
				'block' => 0,
				'cmslinks' => $cms_titles,
				'display_stores_footer' => Configuration::get('PS_STORES_DISPLAY_FOOTER'),
				'display_poweredby' => ((int)Configuration::get('FOOTER_POWEREDBY') === 1 || Configuration::get('FOOTER_POWEREDBY') === false),
				'footer_text' => Configuration::get('FOOTER_CMS_TEXT_'.(int)$this->context->language->id)
			));
			return $this->display(__FILE__, 'blockcms.tpl');
		}
		return '';
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockcms.css', 'all');
	}

	public function getL($key)
	{
		$trad = array(
			'ID' => $this->l('ID'),
			'Name' => $this->l('Name'),
			'There is nothing to display in this CMS category' => $this->l('There is nothing to display in this CMS category')
		);
		return $trad[$key];
	}
}
