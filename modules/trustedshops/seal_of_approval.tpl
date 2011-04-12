{*
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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="tsBox" style="font-size: 80%; width:179px; padding:5px; border: 1px solid #C0C0C0; margin:5px 0 5px 0;" >
<form name="formSiegel" method="post"
	action="https://www.trustedshops.com/shop/certificate.php"
	target="_blank">
	<input name="shop_id" type="hidden"	value="{$trusted_shops_id}">
	<div style="background-color: #FFFFFF; font-family: Verdana, Arial, Helvetica, sans-serif; vertical-align: middle; width: 100%; margin: 0px; padding: 0px;" id="tsInnerBox">
		<div style="text-align: center; width: 100%; float: left; border: 0px solid; margin:2px; padding: 0px;" id="tsSeal">
			<input type="image" style="border: 0px none; margin:0;" src="{$module_dir}/img/seal.png" title="{l s='Trusted Shops seal of approval - click to verify' mod='trustedshops'}">
		</div>
		<div style="text-align: center; line-height: 125%; width: 100%; float: left; border: 0px solid;  margin:0; padding: 0px;" id="tsText">
			<a style="font-weight: normal; text-decoration: none; color: #000000;" title="" href="{$trusted_shops_url}" target="_blank">{$onlineshop_name} {l s='is a certified online shop with Trusted Shops Buyer Protection. More...' mod='trustedshops'}</a>
		</div>
		<div style="clear: both;"></div>
	</div>
</form>
</div>

