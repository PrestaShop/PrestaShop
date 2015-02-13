<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_ADMIN_DIR_'))
	define('_PS_ADMIN_DIR_', getcwd());
require_once(_PS_ADMIN_DIR_.'/../images.inc.php');
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
		$("#'.Tools::htmlentitiesUTF8($id).'").datepicker({
			prevText:"",
			nextText:"",
			dateFormat:"yy-mm-dd"'.($time ? '+time' : '').'});
	});';
}

/**
 * Deprecated since 1.5
 * Use Controller::addJqueryUi('ui.datepicker') instead
 *
 * @param int|array $id id can be a identifier or an array of identifiers
 * @param unknown_type $time
 */
function includeDatepicker($id, $time = false)
{
	Tools::displayAsDeprecated();
	echo '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/ui/jquery.ui.core.min.js"></script>';
	echo '<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'js/jquery/ui/themes/ui-lightness/jquery.ui.theme.css" />';
	echo '<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'js/jquery/ui/themes/ui-lightness/jquery.ui.datepicker.css" />';
	$iso = Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE `id_lang` = '.(int)Context::getContext()->language->id);
	if ($iso != 'en')
		echo '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/ui/i18n/jquery.ui.datepicker-'.Tools::htmlentitiesUTF8($iso).'.js"></script>';
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
function rewriteSettingsFile($baseUrls = null, $theme = null, $arrayDB = null)
{
 	$defines = array();
	$defines['_PS_CACHING_SYSTEM_'] = _PS_CACHING_SYSTEM_;
	$defines['_PS_CACHE_ENABLED_'] = _PS_CACHE_ENABLED_;
	$defines['_DB_NAME_'] = (($arrayDB && isset($arrayDB['_DB_NAME_'])) ? $arrayDB['_DB_NAME_'] : _DB_NAME_);
	$defines['_MYSQL_ENGINE_'] = (($arrayDB && isset($arrayDB['_MYSQL_ENGINE_'])) ? $arrayDB['_MYSQL_ENGINE_'] : _MYSQL_ENGINE_);
	$defines['_DB_SERVER_'] = (($arrayDB && isset($arrayDB['_DB_SERVER_'])) ? $arrayDB['_DB_SERVER_'] : _DB_SERVER_);
	$defines['_DB_USER_'] = (($arrayDB && isset($arrayDB['_DB_USER_'])) ? $arrayDB['_DB_USER_'] : _DB_USER_);
	$defines['_DB_PREFIX_'] = (($arrayDB && isset($arrayDB['_DB_PREFIX_'])) ? $arrayDB['_DB_PREFIX_'] : _DB_PREFIX_);
	$defines['_DB_PASSWD_'] = (($arrayDB && isset($arrayDB['_DB_PASSWD_'])) ? $arrayDB['_DB_PASSWD_'] : _DB_PASSWD_);
	$defines['_COOKIE_KEY_'] = addslashes(_COOKIE_KEY_);
	$defines['_COOKIE_IV_'] = addslashes(_COOKIE_IV_);
	$defines['_PS_CREATION_DATE_'] = addslashes(_PS_CREATION_DATE_);

	if (defined('_RIJNDAEL_KEY_'))
		$defines['_RIJNDAEL_KEY_'] = addslashes(_RIJNDAEL_KEY_);
	if (defined('_RIJNDAEL_IV_'))
		$defines['_RIJNDAEL_IV_'] = addslashes(_RIJNDAEL_IV_);
	$defines['_PS_VERSION_'] = addslashes(_PS_VERSION_);
	$content = "<?php\n\n";
	foreach ($defines as $k => $value)
	{
		if ($k == '_PS_VERSION_')
			$content .= 'if (!defined(\''.$k.'\'))'."\n\t";

		$content .= 'define(\''.$k.'\', \''.addslashes($value).'\');'."\n";
	}
	copy(_PS_ADMIN_DIR_.'/../config/settings.inc.php', _PS_ADMIN_DIR_.'/../config/settings.old.php');
	if ($fd = fopen(_PS_ADMIN_DIR_.'/../config/settings.inc.php', 'w'))
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
function displayDate($sqlDate, $withTime = false)
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
function getPath($urlBase, $id_category, $path = '', $highlight = '', $categoryType = 'catalog', $home = false)
{
	$context = Context::getContext();
	if ($categoryType == 'catalog')
	{
		$category = Db::getInstance()->getRow('
		SELECT id_category, level_depth, nleft, nright
		FROM '._DB_PREFIX_.'category
		WHERE id_category = '.(int)$id_category);
		if (isset($category['id_category']))
		{
			$sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
					WHERE c.nleft <= '.(int)$category['nleft'].'
						AND c.nright >= '.(int)$category['nright'].'
						AND cl.id_lang = '.(int)$context->language->id.
						($home ? ' AND c.id_category='.(int)$id_category : '').'
						AND c.id_category != '.(int)Category::getTopCategory()->id.'
					GROUP BY c.id_category
					ORDER BY c.level_depth ASC
					LIMIT '.(!$home ? (int)($category['level_depth'] + 1) : 1);
			$categories = Db::getInstance()->executeS($sql);
			$fullPath = '';
			$n = 1;
			$nCategories = (int)sizeof($categories);
			foreach ($categories AS $category)
			{
				$link = Context::getContext()->link->getAdminLink('AdminCategories');
				$edit = '<a href="'.Tools::safeOutput($link.'&id_category='.(int)$category['id_category'].'&'.(($category['id_category'] == 1 || $home) ? 'viewcategory' : 'updatecategory')).'" title="'.($category['id_category'] == Category::getRootCategory()->id_category ? 'Home' : 'Modify').'"><i class="icon-'.(($category['id_category'] == Category::getRootCategory()->id_category  || $home) ? 'home' : 'pencil').'"></i></a> ';
				$fullPath .= $edit.
				($n < $nCategories ? '<a href="'.Tools::safeOutput($urlBase.'&id_category='.(int)$category['id_category'].'&viewcategory&token='.Tools::getAdminToken('AdminCategories'.(int)(Tab::getIdFromClassName('AdminCategories')).(int)$context->employee->id)).'" title="'.htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').'">' : '').
				(!empty($highlight) ? str_ireplace($highlight, '<span class="highlight">'.htmlentities($highlight, ENT_NOQUOTES, 'UTF-8').'</span>', $category['name']) : $category['name']).
				($n < $nCategories ? '</a>' : '').
				(($n++ != $nCategories || !empty($path)) ? ' > ' : '');
			}

			return $fullPath.$path;
		}
	}
	elseif ($categoryType == 'cms')
	{
		$category = new CMSCategory($id_category, $context->language->id);
		if (!$category->id)
			return $path;

		$name = ($highlight != null) ? str_ireplace($highlight, '<span class="highlight">'.$highlight.'</span>', CMSCategory::hideCMSCategoryPosition($category->name)) : CMSCategory::hideCMSCategoryPosition($category->name);
		$edit = '<a href="'.Tools::safeOutput($urlBase.'&id_cms_category='.$category->id.'&addcategory&token=' . Tools::getAdminToken('AdminCmsContent'.(int)(Tab::getIdFromClassName('AdminCmsContent')).(int)$context->employee->id)).'">
				<i class="icon-pencil"></i></a> ';
		if ($category->id == 1)
			$edit = '<li><a href="'.Tools::safeOutput($urlBase.'&id_cms_category='.$category->id.'&viewcategory&token=' . Tools::getAdminToken('AdminCmsContent'.(int)(Tab::getIdFromClassName('AdminCmsContent')).(int)$context->employee->id)).'">
					<i class="icon-home"></i></a></li> ';
		$path = $edit.'<li><a href="'.Tools::safeOutput($urlBase.'&id_cms_category='.$category->id.'&viewcategory&token=' . Tools::getAdminToken('AdminCmsContent'.(int)(Tab::getIdFromClassName('AdminCmsContent')).(int)$context->employee->id)).'">
		'.$name.'</a></li> > '.$path;
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
	$upgrader = new Upgrader();

	return $upgrader->checkPSVersion();
}

/**
 * Deprecated since > 1.5.4.1
 * Use Translate::getAdminTranslation($string) instead
 *
 * @param string $string
 */
function translate($string)
{
	Tools::displayAsDeprecated();

	global $_LANGADM;
	if (!is_array($_LANGADM))
		return str_replace('"', '&quot;', $string);
	$key = md5(str_replace('\'', '\\\'', $string));
	$str = (array_key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : ((array_key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : $string);
	return str_replace('"', '&quot;', stripslashes($str));
}

/**
 * Returns a new Tab object
 *
 * @param string $tab class name
 * @return mixed(AdminTab, bool) tab object or false if failed
 */
function checkingTab($tab)
{
	$tab_lowercase = Tools::strtolower(trim($tab));
	if (!Validate::isTabName($tab))
		return false;
	$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT id_tab, module, class_name FROM `'._DB_PREFIX_.'tab` WHERE LOWER(class_name) = \''.pSQL($tab).'\'');
	if (!$row['id_tab'])
	{
		if (isset(AdminTab::$tabParenting[$tab]))
			Tools::redirectAdmin('?tab='.AdminTab::$tabParenting[$tab].'&token='.Tools::getAdminTokenLite(AdminTab::$tabParenting[$tab]));
		echo sprintf(Tools::displayError('Page %s cannot be found..'),$tab);
		return false;
	}

	// Class file is included in Dispatcher::dispatch() function
	if (!class_exists($tab, false) || !$row['id_tab'])
	{
		echo sprintf(Tools::displayError('The class %s cannot be found.'),$tab);
		return false;
	}
	$adminObj = new $tab;
	if (!$adminObj->viewAccess() && ($adminObj->table != 'employee' || Context::getContext()->employee->id != Tools::getValue('id_employee') || !Tools::isSubmit('updateemployee')))
	{
		$adminObj->_errors = array(Tools::displayError('Access denied.'));
		echo $adminObj->displayErrors();
		return false;
	}
	return $adminObj;
}

/**
 * @TODO deprecate for Tab::checkTabRights()
 */
function checkTabRights($id_tab)
{
	static $tabAccesses = null;

	if ($tabAccesses === null)
		$tabAccesses =  Profile::getProfileAccesses(Context::getContext()->employee->id_profile);

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

/**
 * for retrocompatibility with old AdminTab, old index.php
 *
 * @return void
 */
function runAdminTab($tab, $ajaxMode = false)
{
	$ajaxMode = (bool)$ajaxMode;

	require_once(_PS_ADMIN_DIR_.'/init.php');
	$cookie = Context::getContext()->cookie;
	if (empty($tab) && !sizeof($_POST))
	{
		$tab = 'AdminDashboard';
		$_POST['tab'] = $tab;
		$_POST['token'] = Tools::getAdminTokenLite($tab);
	}
	// $tab = $_REQUEST['tab'];
	if ($adminObj = checkingTab($tab))
	{
		Context::getContext()->controller = $adminObj;
		// init is different for new tabs (AdminController) and old tabs (AdminTab)
		if ($adminObj instanceof AdminController)
		{
			if($ajaxMode)
				$adminObj->ajax = true;
			$adminObj->path = dirname($_SERVER["PHP_SELF"]);
			$adminObj->run();
		}
		else
		{
			if (!$ajaxMode)
				require_once(_PS_ADMIN_DIR_.'/header.inc.php');
			$isoUser = Context::getContext()->language->id;
			$tabs = array();
			$tabs = Tab::recursiveTab($adminObj->id, $tabs);
			$tabs = array_reverse($tabs);
			$bread = '';
			foreach ($tabs AS $key => $item)
			{
				$bread .= ' <img src="../img/admin/separator_breadcrumb.png" style="margin-right:5px" alt="&gt;" />';
				if (count($tabs) - 1 > $key)
					$bread .= '<a href="?tab='.$item['class_name'].'&token='.Tools::getAdminToken($item['class_name'].intval($item['id_tab']).(int)Context::getContext()->employee->id).'">';

				$bread .= $item['name'];
				if (count($tabs) - 1 > $key)
					$bread .= '</a>';
			}

			if (!$ajaxMode && Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && Context::getContext()->controller->multishop_context != Shop::CONTEXT_ALL)
			{
				echo '<div class="multishop_info">';
				if (Shop::getContext() == Shop::CONTEXT_GROUP)
				{
					$shop_group = new ShopGroup((int)Shop::getContextShopGroupID());
					printf(Translate::getAdminTranslation('You are configuring your store for group shop %s'), '<b>'.$shop_group->name.'</b>');
				}
				elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
					printf(Translate::getAdminTranslation('You are configuring your store for shop %s'), '<b>'.Context::getContext()->shop->name.'</b>');
				echo '</div>';
			}
			if (Validate::isLoadedObject($adminObj))
			{
				if ($adminObj->checkToken())
				{
					if($ajaxMode)
					{
						// the differences with index.php is here
						$adminObj->ajaxPreProcess();
						$action = Tools::getValue('action');
						// no need to use displayConf() here

						if (!empty($action) && method_exists($adminObj, 'ajaxProcess'.Tools::toCamelCase($action)) )
							$adminObj->{'ajaxProcess'.Tools::toCamelCase($action)}();
						else
							$adminObj->ajaxProcess();

						// @TODO We should use a displayAjaxError
						$adminObj->displayErrors();
						if (!empty($action) && method_exists($adminObj, 'displayAjax'.Tools::toCamelCase($action)) )
							$adminObj->{'displayAjax'.$action}();
						else
							$adminObj->displayAjax();


					}
					else
					{
						/* Filter memorization */
						if (isset($_POST) && !empty($_POST) && isset($adminObj->table))
							foreach ($_POST AS $key => $value)
								if (is_array($adminObj->table))
								{
									foreach ($adminObj->table AS $table)
										if (strncmp($key, $table.'Filter_', 7) === 0 || strncmp($key, 'submitFilter', 12) === 0)
											$cookie->$key = !is_array($value) ? $value : serialize($value);
								}
								elseif (strncmp($key, $adminObj->table.'Filter_', 7) === 0 || strncmp($key, 'submitFilter', 12) === 0)
									$cookie->$key = !is_array($value) ? $value : serialize($value);

						if (isset($_GET) && !empty($_GET) && isset($adminObj->table))
							foreach ($_GET AS $key => $value)
								if (is_array($adminObj->table))
								{
									foreach ($adminObj->table AS $table)
										if (strncmp($key, $table.'OrderBy', 7) === 0 || strncmp($key, $table.'Orderway', 8) === 0)
											$cookie->$key = $value;
								}
								elseif (strncmp($key, $adminObj->table.'OrderBy', 7) === 0 || strncmp($key, $adminObj->table.'Orderway', 12) === 0)
									$cookie->$key = $value;
						$adminObj->displayConf();
						$adminObj->postProcess();
						$adminObj->displayErrors();
						$adminObj->display();
						include(_PS_ADMIN_DIR_.'/footer.inc.php');
					}
				}
				else
				{
					if($ajaxMode)
					{
						// If this is an XSS attempt, then we should only display a simple, secure page
						if (ob_get_level() && ob_get_length() > 0)
							ob_clean();

						// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
						$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$adminObj->token.'$2', $_SERVER['REQUEST_URI']);
						if (false === strpos($url, '?token=') && false === strpos($url, '&token='))
							$url .= '&token='.$adminObj->token;


						// we can display the correct url
						// die(Tools::jsonEncode(array(Translate::getAdminTranslation('Invalid security token'),$url)));
						die(Tools::jsonEncode(Translate::getAdminTranslation('Invalid security token')));
					}
					else
					{
						// If this is an XSS attempt, then we should only display a simple, secure page
						if (ob_get_level() && ob_get_length() > 0)
							ob_clean();

						// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
						$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$adminObj->token.'$2', $_SERVER['REQUEST_URI']);
						if (false === strpos($url, '?token=') && false === strpos($url, '&token='))
							$url .= '&token='.$adminObj->token;

						$message = Translate::getAdminTranslation('Invalid security token');
						echo '<html><head><title>'.$message.'</title></head><body style="font-family:Arial,Verdana,Helvetica,sans-serif;background-color:#EC8686">
							<div style="background-color:#FAE2E3;border:1px solid #000000;color:#383838;font-weight:700;line-height:20px;margin:0 0 10px;padding:10px 15px;width:500px">
								<img src="../img/admin/error2.png" style="margin:-4px 5px 0 0;vertical-align:middle">
								'.$message.'
							</div>';
						echo '<a href="'.htmlentities($url).'" method="get" style="float:left;margin:10px">
								<input type="button" value="'.Tools::htmlentitiesUTF8(Translate::getAdminTranslation('I understand the risks and I really want to display this page')).'" style="height:30px;margin-top:5px" />
							</a>
							<a href="index.php" method="get" style="float:left;margin:10px">
								<input type="button" value="'.Tools::htmlentitiesUTF8(Translate::getAdminTranslation('Take me out of here!')).'" style="height:40px" />
							</a>
						</body></html>';
						die;
					}
				}
			}
		}
	}
}
