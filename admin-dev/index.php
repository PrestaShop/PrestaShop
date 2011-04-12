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

define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');
include(PS_ADMIN_DIR.'/header.inc.php');
if (empty($tab) and !sizeof($_POST))
{
	$tab = 'AdminHome';
	$_POST['tab'] = 'AdminHome';
	$_POST['token'] = Tools::getAdminToken($tab.intval(checkingTab($tab)).intval($cookie->id_employee));
}


	if ($id_tab = checkingTab($tab))
	{
    	$isoUser = Language::getIsoById(intval($cookie->id_lang));
		$tabs = array();
		recursiveTab($id_tab);
		$tabs = array_reverse($tabs);
		$bread = '';
		$country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));

		foreach ($tabs AS $key => $item)
			$bread .= ' <img src="../img/admin/separator_breadcrum.png" style="margin-right:5px" />
			'.((sizeof($tabs) - 1 > $key)
				? '<a href="?tab='.$item['class_name'].'&token='.Tools::getAdminToken($item['class_name'].intval($item['id_tab']).intval($cookie->id_employee)).'">'
				: '').'
			'.$item['name'].((sizeof($tabs) - 1 > $key) ? '</a>' : '');

		echo'<script type="text/javascript">

		$(function() {
			$.ajax({
				type: \'POST\',
				url: \'ajax.php\',
				data: \'helpAccess=1&item='.$item['class_name'].'&isoUser='.$isoUser.'&country='.$country->iso_code.'&version='._PS_VERSION_.'\',
				async : true,
				success: function(msg) {
					$("#help-button").html(msg);
					$("#help-button").fadeIn("slow");
				}
			});
		});</script>';


		echo '<div class="path_bar">
		<div id="help-button" class="floatr" style="display: none; font-family: Verdana; font-size: 10px; margin-right: 4px; margin-top: 4px;">
		</div>
			<a href="?token='.Tools::getAdminToken($tab.intval(Tab::getIdFromClassName($tab)).intval($cookie->id_employee)).'">'.translate('Back Office').'</a>
			'.$bread.'
		</div>';

		if (Validate::isLoadedObject($adminObj))
		{
			if ($adminObj->checkToken())
			{
				/* Filter memorization */
				if (isset($_POST) AND !empty($_POST) AND isset($adminObj->table))
					foreach ($_POST AS $key => $value)
						if (is_array($adminObj->table))
						{
							foreach ($adminObj->table AS $table)
								if (strncmp($key, $table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
									$cookie->$key = !is_array($value) ? $value : serialize($value);
						}
						elseif (strncmp($key, $adminObj->table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
							$cookie->$key = !is_array($value) ? $value : serialize($value);

				if (isset($_GET) AND !empty($_GET) AND isset($adminObj->table))
					foreach ($_GET AS $key => $value)
						if (is_array($adminObj->table))
						{
							foreach ($adminObj->table AS $table)
								if (strncmp($key, $table.'OrderBy', 7) === 0 OR strncmp($key, $table.'Orderway', 8) === 0)
									$cookie->$key = $value;
						}
						elseif (strncmp($key, $adminObj->table.'OrderBy', 7) === 0 OR strncmp($key, $adminObj->table.'Orderway', 12) === 0)
							$cookie->$key = $value;

				$adminObj->displayConf();
				$adminObj->postProcess();
				$adminObj->displayErrors();
				$adminObj->display();
			}
			else
			{
				// If this is an XSS attempt, then we should only display a simple, secure page
				ob_clean();

				// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
				$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$adminObj->token.'$2', $_SERVER['REQUEST_URI']);
				if (false === strpos($url, '?token=') AND false === strpos($url, '&token='))
					$url .= '&token='.$adminObj->token;

				$message = translate('Invalid security token');
				echo '<html><head><title>'.$message.'</title></head><body style="font-family:Arial,Verdana,Helvetica,sans-serif;background-color:#EC8686">
					<div style="background-color:#FAE2E3;border:1px solid #000000;color:#383838;font-weight:700;line-height:20px;margin:0 0 10px;padding:10px 15px;width:500px">
						<img src="../img/admin/error2.png" style="margin:-4px 5px 0 0;vertical-align:middle">
						'.$message.'
					</div>';
				echo '<a href="'.htmlentities($url).'" method="get" style="float:left;margin:10px">
						<input type="button" value="'.Tools::htmlentitiesUTF8(translate('I understand the risks and I really want to display this page')).'" style="height:30px;margin-top:5px" />
					</a>
					<a href="index.php" method="get" style="float:left;margin:10px">
						<input type="button" value="'.Tools::htmlentitiesUTF8(translate('Take me out of here!')).'" style="height:40px" />
					</a>
				</body></html>';
				die;
			}
		}
	}

include(PS_ADMIN_DIR.'/footer.inc.php');

