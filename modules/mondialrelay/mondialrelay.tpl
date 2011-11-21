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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{$jQueryOverload}

<link href="{$new_base_dir}/style.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript">
	// Global JS Value
	var _PS_MR_MODULE_DIR_ = "{$new_base_dir}";
	var mrtoken = "{$MRToken}";
	var PS_MROPC = {$one_page_checkout};
	var PS_MRTranslationList = new Array();
	var PS_MRCarrierMethodList =  new Array();
	var PS_MRSelectedRelayPoint = {literal}{{/literal}'carrier_id': 0, 'relayPointNum': 0{literal}}{/literal};
	var PS_MRPreSelectedRelay = '{$preSelectedRelay}';

	PS_MRTranslationList['Select'] = "{l s='Select' mod='mondialrelay'}";
	PS_MRTranslationList['Selected'] = "{l s='Selected' mod='mondialrelay'}";
	PS_MRTranslationList['errorSelection'] = "{l s='Please choose a relay point' mod='mondialrelay'}";
	PS_MRTranslationList['openingRelay'] = "{l s='Opening hours' mod='mondialrelay'}";
	PS_MRTranslationList['moreDetails'] = "{l s='More details' mod='mondialrelay'}";
</script>

<script type="text/javascript" src="{$new_base_dir}mondialrelay.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="{$new_base_dir}js/gmap.js"></script>

<script type="text/javascript">

	$(document).ready(function()
	{literal}{{/literal}
		// Bind id_carrierX to an ajax call
		{foreach from=$carriersextra item=carrier name=myLoop}
			$('#id_carrier' + {$carrier.id_carrier}).click(function()
			{literal}{{/literal}
				PS_MRCarrierSelectedProcess($(this), {$carrier.id_carrier}, '{$carrier.mr_ModeLiv}');
			{literal}}{/literal});
			PS_MRCarrierMethodList[{$carrier.id_carrier}] = {$carrier.id_mr_method};
			if ($('#id_carrier' + {$carrier.id_carrier}).attr('checked'))
			{literal}{{/literal}
				PS_MRCarrierSelectedProcess($('#id_carrier' + {$carrier.id_carrier}), {$carrier.id_carrier}, '{$carrier.mr_ModeLiv}');
			{literal}}{/literal}
		{/foreach}
		// Handle input click of the other input to hide the previous relay point list displayed
		$('input[name=id_carrier]').click(function()
		{literal}{{/literal}
			// Hide MR input if one of them is not selected
			if (PS_MRCarrierMethodList[$(this).val()] == undefined)
				PS_MRHideLastRelayPointList();
		{literal}}{/literal})
	{literal}}{/literal});
	</script>
