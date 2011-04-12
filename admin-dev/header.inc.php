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

// P3P Policies (http://www.w3.org/TR/2002/REC-P3P-20020416/#compact_policies)
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once(dirname(__FILE__).'/init.php');

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$iso.'" lang="'.$iso.'">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="'._PS_JS_DIR_.'jquery/datepicker/datepicker.css" />
		<link type="text/css" rel="stylesheet" href="'._PS_CSS_DIR_.'admin.css" />
		<link type="text/css" rel="stylesheet" href="'._PS_CSS_DIR_.'jquery.cluetip.css" />
		<link type="text/css" rel="stylesheet" href="themes/'.$employee->bo_theme.'/admin.css" />
		<title>PrestaShop&trade; - '.translate('Administration panel').'</title>
		<script type="text/javascript">
			var helpboxes = '.Configuration::get('PS_HELPBOX').';
			var roundMode = '.Configuration::get('PS_PRICE_ROUND_MODE').';
		</script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/jquery.hoverIntent.minified.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/jquery.cluetip.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'admin.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'toggle.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'tools.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'ajax.js"></script>
		<link rel="shortcut icon" href="'._PS_IMG_.'favicon.ico" />
		'.Module::hookExec('backOfficeHeader').'
		<!--[if IE]>
		<link type="text/css" rel="stylesheet" href="'._PS_CSS_DIR_.'admin-ie.css" />
		<![endif]-->
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {
				color:'.(Tools::getBrightness(empty($employee->bo_color) ? '#FFFFFF' : $employee->bo_color) < 128 ? 'white' : '#383838').'
			}
		</style>
	</head>
	<body '.((!empty($employee->bo_color)) ? 'style="background:'.Tools::htmlentitiesUTF8($employee->bo_color).'"' : '').'>
	<div id="top_container">
		<div id="container">
			<div id="header_infos"><span>
				<a id="header_shopname" href="index.php"><span>'.Configuration::get('PS_SHOP_NAME').'</span></a><br />
				'.Tools::substr($employee->firstname, 0, 1).'.&nbsp;'.htmlentities($employee->lastname, ENT_COMPAT, 'UTF-8').'
				[ <a href="index.php?logout" id="header_logout"><span>'.translate('logout').'</span></a> ]
				- <a href="'.__PS_BASE_URI__.'" id="header_foaccess" target="_blank" title="'.translate('View my shop').'"><span>'.translate('View my shop').'</span></a>
				- <a href="index.php?tab=AdminEmployees&id_employee='.(int)$cookie->id_employee.'&updateemployee&token='.Tools::getAdminTokenLite('AdminEmployees').'" style="font-size: 10px;"><img src="../img/admin/employee.gif" alt="" /> '.translate('My preferences').'</a>
			</span></div>
			<div id="header_search">
				<form method="post" action="index.php?tab=AdminSearch&token='.Tools::getAdminTokenLite('AdminSearch').'">
					<input type="text" name="bo_query" id="bo_query"
						value="'.Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))).'"
					/>
					<select name="bo_search_type" id="bo_search_type">
						<option value="0">'.translate('everywhere').'</option>
						<option value="1" '.(Tools::getValue('bo_search_type') == 1 ? 'selected="selected"' : '').'>'.translate('catalog').'</option>
						<option value="2" '.(Tools::getValue('bo_search_type') == 2 ? 'selected="selected"' : '').'>'.translate('customers').'</option>
						<option value="3" '.(Tools::getValue('bo_search_type') == 3 ? 'selected="selected"' : '').'>'.translate('orders').'</option>
						<option value="4" '.(Tools::getValue('bo_search_type') == 4 ? 'selected="selected"' : '').'>'.translate('invoices').'</option>
						<option value="5" '.(Tools::getValue('bo_search_type') == 5 ? 'selected="selected"' : '').'>'.translate('carts').'</option>
					</select>
					<input type="submit" id="bo_search_submit" class="button" value="'.translate('Search').'"/>
				</form>
			</div>
			<div id="header_quick">
				<script type="text/javascript">
				function quickSelect(elt)
				{
					var eltVal = $(elt).val();
					if (eltVal == "0") return false;
					else if (eltVal.substr(eltVal.length - 6) == "_blank") window.open(eltVal.substr(0, eltVal.length - 6), "_blank");
					else location.href = eltVal;
				}
				</script>
				<select onchange="quickSelect(this);" id="quick_select">
					<option value="0">'.translate('Quick Access').'</option>';
foreach (QuickAccess::getQuickAccesses((int)($cookie->id_lang)) AS $quick)
{
	preg_match('/tab=(.+)(&.+)?$/', $quick['link'], $adminTab);
	if (isset($adminTab[1]))
	{
		if (strpos($adminTab[1], '&'))
			$adminTab[1] = substr($adminTab[1], 0, strpos($adminTab[1], '&'));
		$quick['link'] .= '&token='.Tools::getAdminToken($adminTab[1].(int)(Tab::getIdFromClassName($adminTab[1])).(int)($cookie->id_employee));
	}
	echo '<option value="'.$quick['link'].($quick['new_window'] ? '_blank' : '').'">&gt; '.$quick['name'].'</option>';
}
echo '			</select>
			</div>
			<div class="flatclear">&nbsp;</div>
			'.Module::hookExec('backOfficeTop').'
			<ul id="menu">';
if (empty($tab))
	echo '<div class="mainsubtablist" style="display:none"></div>';

$id_parent_tab_current = (int)(Tab::getCurrentParentId());
$tabs = Tab::getTabs((int)($cookie->id_lang), 0);
$echoLis = '';
$mainsubtablist = '';
foreach ($tabs AS $t)
	if (checkTabRights($t['id_tab']) === true)
	{
		$img = (Tools::file_exists_cache(($path_name = _PS_ADMIN_DIR_.'/themes/'.$employee->bo_theme.'/img/t/').$t['class_name'].'.gif') ? 'themes/'.$employee->bo_theme.'/img/' : _PS_IMG_.'/').'t/'.$t['class_name'].'.gif';
		if (trim($t['module']) != '')
			$img = _MODULE_DIR_.$t['module'].'/'.$t['class_name'].'.gif';
		$current = (($t['class_name'] == $tab) OR ($id_parent_tab_current == $t['id_tab']));
		echo '<li class="submenu_size '.($current ? 'active' : '').'" id="maintab'.$t['id_tab'].'">
			<a href="index.php?tab='.$t['class_name'].'&token='.Tools::getAdminToken($t['class_name'].(int)($t['id_tab']).(int)($cookie->id_employee)).'">
				<img src="'.$img.'" alt="" /> '.$t['name'].'
			</a>
		</li>';
		$echoLi = '';
		$subTabs = Tab::getTabs((int)($cookie->id_lang), (int)($t['id_tab']));
		foreach ($subTabs AS $t2)
			if (checkTabRights($t2['id_tab']) === true)
				$echoLi .= '<li><a href="index.php?tab='.$t2['class_name'].'&token='.Tools::getAdminTokenLite($t2['class_name']).'">'.$t2['name'].'</a></li>';
		if ($current)
			$mainsubtablist = $echoLi;
		$echoLis .= '<div id="tab'.(int)($t['id_tab']).'_subtabs" style="display:none">'.$echoLi.'</div>';
	}
echo '		</ul>'.$echoLis;
if ($employee->bo_uimode == 'hover')
	echo '	<script type="text/javascript">
				$("#menu li").hoverIntent({over:hoverTabs,timeout:100,out:outTabs});
				function outTabs(){}
				function hoverTabs() {
					var content = $("#tab"+parseInt(this.id.substr(7, 3))+"_subtabs").html();
					$("#submenu").html(content);
					if (content.length == 0)
						$("#submenu").removeClass("withLeftBorder");
					else
						$("#submenu").addClass("withLeftBorder");
					$("#menu li").removeClass("active");
					$(this).addClass("active");
				}
			</script>';
echo '		<ul id="submenu" '.(strlen($mainsubtablist) ? 'class="withLeftBorder"' : '').'>'.$mainsubtablist.'</ul>
			<div id="main">
				<div id="content">'
			.(file_exists(PS_ADMIN_DIR.'/../install') ? '<div style="background-color: #FFEBCC;border: 1px solid #F90;line-height: 20px;margin: 0px 0px 10px;padding: 10px 20px;">'
				.translate('For security reasons, you must also:').' '.
				translate('deleted the /install folder').
				'</div>' : '').'
				';
