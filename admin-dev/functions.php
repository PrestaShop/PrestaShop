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

require_once(dirname(__FILE__).'/../images.inc.php'); 

function bindDatepicker($id, $time)
{
	if ($time)
	echo '
		var dateObj = new Date();
		var hours = dateObj.getHours();
		var mins = dateObj.getMinutes();
		var secs = dateObj.getSeconds();
		if (hours < 10) { hours = "0" + hours; }
		if (mins < 10) { mins = "0" + mins; }
		if (secs < 10) { secs = "0" + secs; }
		var time = " "+hours+":"+mins+":"+secs;';

	echo '
	$(function() {
		$("#'.$id.'").datepicker({
			prevText:"",
			nextText:"",
			dateFormat:"yy-mm-dd"'.($time ? '+time' : '').'});
	});';
}

// id can be a identifier or an array of identifiers
function includeDatepicker($id, $time = false)
{
	global $cookie;
	echo '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery-ui-1.8.10.custom.min.js"></script>';
	$iso = Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE `id_lang` = '.(int)($cookie->id_lang));
	if ($iso != 'en')
		echo '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/datepicker/ui/i18n/ui.datepicker-'.$iso.'.js"></script>';
	echo '<script type="text/javascript">';
		if (is_array($id))
			foreach ($id as $id2)
				bindDatepicker($id2, $time);
		else
			bindDatepicker($id, $time);
	echo '</script>';
}

/**
  * Generate a new settings file, only transmitted parameters are updated
  *
  * @param string $baseUri Base URI
  * @param string $theme Theme name (eg. default)
  * @param array $arrayDB Parameters in order to connect to database
  */
function	rewriteSettingsFile($baseUrls = NULL, $theme = NULL, $arrayDB = NULL)
{
 	$defines = array();
	$defines['__PS_BASE_URI__'] = ($baseUrls AND $baseUrls['__PS_BASE_URI__']) ? $baseUrls['__PS_BASE_URI__'] : __PS_BASE_URI__;
	$defines['_MEDIA_SERVER_1_'] = ($baseUrls AND isset($baseUrls['_MEDIA_SERVER_1_'])) ? $baseUrls['_MEDIA_SERVER_1_'] : _MEDIA_SERVER_1_;
	$defines['_MEDIA_SERVER_2_'] = ($baseUrls AND isset($baseUrls['_MEDIA_SERVER_2_'])) ? $baseUrls['_MEDIA_SERVER_2_'] : _MEDIA_SERVER_2_;
	$defines['_MEDIA_SERVER_3_'] = ($baseUrls AND isset($baseUrls['_MEDIA_SERVER_3_'])) ? $baseUrls['_MEDIA_SERVER_3_'] : _MEDIA_SERVER_3_;
	$defines['_PS_CACHING_SYSTEM_'] = _PS_CACHING_SYSTEM_;
	$defines['_PS_CACHE_ENABLED_'] = _PS_CACHE_ENABLED_;
	$defines['_THEME_NAME_'] = $theme ? $theme : _THEME_NAME_;
	$defines['_DB_NAME_'] = (($arrayDB AND isset($arrayDB['_DB_NAME_'])) ? $arrayDB['_DB_NAME_'] : _DB_NAME_);
	$defines['_MYSQL_ENGINE_'] = _MYSQL_ENGINE_;
	$defines['_DB_SERVER_'] = (($arrayDB AND isset($arrayDB['_DB_SERVER_'])) ? $arrayDB['_DB_SERVER_'] : _DB_SERVER_);
	$defines['_DB_USER_'] = (($arrayDB AND isset($arrayDB['_DB_USER_'])) ? $arrayDB['_DB_USER_'] : _DB_USER_);
	$defines['_DB_PREFIX_'] = (($arrayDB AND isset($arrayDB['_DB_PREFIX_'])) ? $arrayDB['_DB_PREFIX_'] : _DB_PREFIX_);
	$defines['_DB_PASSWD_'] = (($arrayDB AND isset($arrayDB['_DB_PASSWD_'])) ? $arrayDB['_DB_PASSWD_'] : _DB_PASSWD_);
	$defines['_DB_TYPE_'] = (($arrayDB AND isset($arrayDB['_DB_TYPE_'])) ? $arrayDB['_DB_TYPE_'] : _DB_TYPE_);
	$defines['_COOKIE_KEY_'] = addslashes(_COOKIE_KEY_);
	$defines['_COOKIE_IV_'] = addslashes(_COOKIE_IV_);
	if (defined('_RIJNDAEL_KEY_'))
		$defines['_RIJNDAEL_KEY_'] = addslashes(_RIJNDAEL_KEY_);
	if (defined('_RIJNDAEL_IV_'))
		$defines['_RIJNDAEL_IV_'] = addslashes(_RIJNDAEL_IV_);
	$defines['_PS_VERSION_'] = addslashes(_PS_VERSION_);
	$content = "<?php\n\n";
	foreach ($defines as $k => $value)
		$content .= 'define(\''.$k.'\', \''.addslashes($value).'\');'."\n";
	$content .= "\n?>";
	if ($fd = @fopen(PS_ADMIN_DIR.'/../config/settings.inc.php', 'w'))
	{
		fwrite($fd, $content);
		fclose($fd);
		return true;
	}
	return false;
}

/**
  * Display SQL date in friendly format
  *
  * @param string $sqlDate Date in SQL format (YYYY-MM-DD HH:mm:ss)
  * @param boolean $withTime Display both date and time
  * @todo Several formats (french : DD-MM-YYYY)
  */
function	displayDate($sqlDate, $withTime = false)
{
	return strftime('%Y-%m-%d'.($withTime ? ' %H:%M:%S' : ''), strtotime($sqlDate));
}

/**
  * Return path to a product category
  *
  * @param string $urlBase Start URL
  * @param integer $id_category Start category
  * @param string $path Current path
  * @param string $highlight String to highlight (in XHTML/CSS)
  * @param string $type Category type (products/cms)
  */
function getPath($urlBase, $id_category, $path = '', $highlight = '', $categoryType = 'catalog')
{
	global $cookie;
	
	if ($categoryType == 'catalog')
	{			
		$category = Db::getInstance()->getRow('
		SELECT id_category, level_depth, nleft, nright
		FROM '._DB_PREFIX_.'category
		WHERE id_category = '.(int)$id_category);

		if (isset($category['id_category']))
		{
			$categories = Db::getInstance()->ExecuteS('
			SELECT c.id_category, cl.name, cl.link_rewrite
			FROM '._DB_PREFIX_.'category c
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
			WHERE c.nleft <= '.(int)$category['nleft'].' AND c.nright >= '.(int)$category['nright'].' AND cl.id_lang = '.(int)($cookie->id_lang).'
			ORDER BY c.level_depth ASC
			LIMIT '.(int)($category['level_depth'] + 1));
			
			$fullPath = '';
			$n = 1;
			$nCategories = (int)sizeof($categories);
			foreach ($categories AS $category)
			{
				$edit = '<a href="'.$urlBase.'&id_category='.(int)$category['id_category'].'&'.($category['id_category'] == 1 ? 'viewcategory' : 'addcategory').'&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" title="'.($category['id_category'] == 1 ? 'Home' : 'Modify').'"><img src="../img/admin/'.($category['id_category'] == 1 ? 'home' : 'edit').'.gif" alt="" /></a> ';
				$fullPath .= $edit.
				($n < $nCategories ? '<a href="'.$urlBase.'&id_category='.(int)$category['id_category'].'&viewcategory&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" title="'.htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').'">' : '').
				(!empty($highlight) ? str_ireplace($highlight, '<span class="highlight">'.htmlentities($highlight, ENT_NOQUOTES, 'UTF-8').'</span>', $category['name']) : $category['name']).
				($n < $nCategories ? '</a>' : '').
				(($n++ != $nCategories OR !empty($path)) ? ' > ' : '');
			}
				
			return $fullPath.$path;
		}
	}
	elseif ($categoryType == 'cms')
	{
		$category = new CMSCategory($id_category, (int)($cookie->id_lang));
		if (!$category->id)
			return $path;

		$name = ($highlight != NULL) ? str_ireplace($highlight, '<span class="highlight">'.$highlight.'</span>', CMSCategory::hideCMSCategoryPosition($category->name)) : CMSCategory::hideCMSCategoryPosition($category->name);
		$edit = '<a href="'.$urlBase.'&id_cms_category='.$category->id.'&addcategory&token=' . Tools::getAdminToken('AdminCMSContent'.(int)(Tab::getIdFromClassName('AdminCMSContent')).(int)($cookie->id_employee)).'">
				<img src="../img/admin/edit.gif" alt="Modify" /></a> ';
		if ($category->id == 1)
			$edit = '<a href="'.$urlBase.'&id_cms_category='.$category->id.'&viewcategory&token=' . Tools::getAdminToken('AdminCMSContent'.(int)(Tab::getIdFromClassName('AdminCMSContent')).(int)($cookie->id_employee)).'">
					<img src="../img/admin/home.gif" alt="Home" /></a> ';
		$path = $edit.'<a href="'.$urlBase.'&id_cms_category='.$category->id.'&viewcategory&token=' . Tools::getAdminToken('AdminCMSContent'.(int)(Tab::getIdFromClassName('AdminCMSContent')).(int)($cookie->id_employee)).'">
		'.$name.'</a> > '.$path;
		if ($category->id == 1)
			return substr($path, 0, strlen($path) - 3);
		return getPath($urlBase, $category->id_parent, $path, '', 'cms');
	}
}

function getDirContent($path)
{
	$content = array();
	if (is_dir($path))
	{
		$d = dir($path);
		while (false !== ($entry = $d->read()))
			if ($entry{0} != '.')
				$content[] = $entry;
		$d->close();
	}
	return $content;
}

function createDir($path, $rights)
{
	if (file_exists($path))
		return true;
	return @mkdir($path, $rights);
}

function checkPSVersion()
{
	libxml_set_streams_context(stream_context_create(array('http' => array('timeout' => 3))));
	if ($feed = @simplexml_load_file('http://www.prestashop.com/xml/version.xml') AND _PS_VERSION_ < $feed->version->num)
		return array('name' => $feed->version->name, 'link' => $feed->download->link);
	return false;
}

function translate($string)
{
	global $_LANGADM;
	if (!is_array($_LANGADM))
		return str_replace('"', '&quot;', $string);
	$key = md5(str_replace('\'', '\\\'', $string));
	$str = (key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : ((key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : $string);
	return str_replace('"', '&quot;', stripslashes($str));
}

function recursiveTab($id_tab)
{
	global $cookie, $tabs;
	
	$adminTab = Tab::getTab((int)($cookie->id_lang), $id_tab);
	$tabs[]= $adminTab;
	if ($adminTab['id_parent'] > 0)
		recursiveTab($adminTab['id_parent']);
}

function checkingTab($tab)
{
	global $adminObj, $cookie;

	$tab = trim($tab);
	if (!Validate::isTabName($tab))
		return false;
	if (!($id_tab = Tab::getIdFromClassName($tab)))
	{
		if (isset(AdminTab::$tabParenting[$tab]))
			Tools::redirectAdmin('?tab='.AdminTab::$tabParenting[$tab].'&token='.Tools::getAdminTokenLite(AdminTab::$tabParenting[$tab]));
		echo Tools::displayError('Tab cannot be found.');
		return false;
	}
	if ($module = Db::getInstance()->getValue('SELECT module FROM '._DB_PREFIX_.'tab WHERE class_name = \''.pSQL($tab).'\'') AND file_exists(_PS_MODULE_DIR_.'/'.$module.'/'.$tab.'.php'))
		include_once(_PS_MODULE_DIR_.'/'.$module.'/'.$tab.'.php');
	elseif (file_exists(PS_ADMIN_DIR.'/tabs/'.$tab.'.php'))
		include_once(PS_ADMIN_DIR.'/tabs/'.$tab.'.php');

	if (!class_exists($tab, false) OR !$id_tab)
	{
		echo Tools::displayError('Tab file cannot be found.');
		return false;
	}
	$adminObj = new $tab;
	if (!$adminObj->viewAccess() AND ($adminObj->table != 'employee' OR $cookie->id_employee != Tools::getValue('id_employee') OR !Tools::isSubmit('updateemployee')))
	{
		$adminObj->_errors = array(Tools::displayError('Access denied'));
		echo $adminObj->displayErrors();
		return false;
	}
	return ($id_tab);
}

function checkTabRights($id_tab)
{
	global $cookie;
	static $tabAccesses = NULL;
	
	if ($tabAccesses === NULL)
		$tabAccesses =  Profile::getProfileAccesses($cookie->profile);

	if (isset($tabAccesses[(int)($id_tab)]['view']))
		return ($tabAccesses[(int)($id_tab)]['view'] === '1');
	return false;
}


/**
     * Converts a simpleXML element into an array. Preserves attributes and everything.
     * You can choose to get your elements either flattened, or stored in a custom index that
     * you define.
     * For example, for a given element
     * <field name="someName" type="someType"/>
     * if you choose to flatten attributes, you would get:
     * $array['field']['name'] = 'someName';
     * $array['field']['type'] = 'someType';
     * If you choose not to flatten, you get:
     * $array['field']['@attributes']['name'] = 'someName';
     * _____________________________________
     * Repeating fields are stored in indexed arrays. so for a markup such as:
     * <parent>
     * <child>a</child>
     * <child>b</child>
     * <child>c</child>
     * </parent>
     * you array would be:
     * $array['parent']['child'][0] = 'a';
     * $array['parent']['child'][1] = 'b';
     * ...And so on.
     * _____________________________________
     * @param simpleXMLElement $xml the XML to convert
     * @param boolean $flattenValues    Choose wether to flatten values
     *                                    or to set them under a particular index.
     *                                    defaults to true;
     * @param boolean $flattenAttributes Choose wether to flatten attributes
     *                                    or to set them under a particular index.
     *                                    Defaults to true;
     * @param boolean $flattenChildren    Choose wether to flatten children
     *                                    or to set them under a particular index.
     *                                    Defaults to true;
     * @param string $valueKey            index for values, in case $flattenValues was set to
            *                            false. Defaults to "@value"
     * @param string $attributesKey        index for attributes, in case $flattenAttributes was set to
            *                            false. Defaults to "@attributes"
     * @param string $childrenKey        index for children, in case $flattenChildren was set to
            *                            false. Defaults to "@children"
     * @return array the resulting array.
     */
function simpleXMLToArray ($xml, $flattenValues = true, $flattenAttributes = true, $flattenChildren = true, $valueKey = '@value', $attributesKey = '@attributes', $childrenKey = '@children')
{
	$return = array();
	if (!($xml instanceof SimpleXMLElement))
		return $return;

	$name = $xml->getName();
	$_value = trim((string)$xml);
	if (strlen($_value) == 0)
		$_value = null;

	if ($_value !== null)
	{
		if (!$flattenValues)
			$return[$valueKey] = $_value;
		else
			$return = $_value;
	}

	$children = array();
	$first = true;
	foreach($xml->children() as $elementName => $child)
	{
		$value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
		if (isset($children[$elementName]))
		{
			if ($first)
			{
				$temp = $children[$elementName];
				unset($children[$elementName]);
				$children[$elementName][] = $temp;
				$first=false;
			}
			$children[$elementName][] = $value;
		}
		else
			$children[$elementName] = $value;
	}

	if (count($children) > 0 )
	{
		if (!$flattenChildren)
			$return[$childrenKey] = $children;
		else
			$return = array_merge($return, $children);
	}

	$attributes = array();
	foreach($xml->attributes() as $name => $value)
		$attributes[$name] = trim($value);

	if (count($attributes) > 0)
	{
		if (!$flattenAttributes)
			$return[$attributesKey] = $attributes;
		else
			$return = array_merge($return, $attributes);
	}

	return $return;
}
