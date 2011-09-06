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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2011 PrestaShop SA
*	@version	Release: $Revision$
*	@license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_ADMIN_DIR_.'/tabs/AdminPreferences.php');

class AdminUpgrade extends AdminPreferences
{

	public function display()
	{
		global $cookie, $currentIndex;
		echo '<fieldset><legend><img src="'.__PS_BASE_URI__.'modules/autoupgrade/logo.gif" />'.$this->l('Upgrade').'</legend>';
		if(is_dir('autoupgrade'))
		{
		echo '<p>'.$this->l('To make an upgrade, you need to activate the autoupgrade module').'</p>';
		$tokenModule = Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)$cookie->id_employee);
		$tokenAdminTabs =  Tools::getAdminToken('AdminTabs'.(int)(Tab::getIdFromClassName('AdminTabs')).(int)$cookie->id_employee);
		$tokenAdminTools =  Tools::getAdminToken('AdminTools'.(int)(Tab::getIdFromClassName('AdminTools')).(int)$cookie->id_employee);
		echo '<br/><p id="ContainerActivateButton">
			<a class="button" id="activateAutoupgradeModule" href="index.php?tab=AdminModules&token='.$tokenModule.'&amp;install=autoupgrade&amp;module_name=autoupgrade">'
			.$this->l('Activate the module').'</a></p>';
		echo '<script type="text/javascript">
			$("#activateAutoupgradeModule").click(function(e){
				e.preventDefault();
				console.warn($(this).attr("href"));
				req = $.ajax({
					type:"POST",
					url : "index.php",
				async: true,
				data : {
					ajaxMode : "1",
					token : "'.$tokenModule.'",
					tab : "AdminModules",
					install: "autoupgrade",
					module_name:"autoupgrade",
				},
			success : function(res,textStatus,jqXHR)
			{
				console.log(res);
				$.get("index.php?token='.$tokenAdminTabs.'&tab=AdminTabs&id_tab='.Tab::getIdFromClassName('AdminUpgrade').'&deletetab",
				function(e){
					alert("'.$this->l('Previous UpgradeTab has been removed. Please go to the new Upgrade tab').'");
					window.location = "'.'index.php?token='.$tokenAdminTools.'&tab=AdminTools&id_tab='.Tab::getIdFromClassName('AdminTools').'";

				}
				);
			},
			error: function(res,textStatus,jqXHR)
			{
				alert("'.$this->l("Unable to install the module automatically. Please manually remove the Upgrade AdminTab and install the autoupgrade module.").'");
			}
		});
	});
				
		</script>';
		}
		else{
				echo "please download autoupgrade at this url";
		}
		echo '</fieldset>';
	}

}

