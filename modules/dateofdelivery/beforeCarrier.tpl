{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $datesDelivery|count}
	<script type="text/javascript">
	{literal}
		var datesDelivery = {};
	{/literal}
	{foreach $datesDelivery as $by_address}
		datesDelivery[{$by_address@key}] = {};
		{foreach $by_address as $date}
			{if $date && isset($date[0])}
				datesDelivery[{$by_address@key}]["{$date@key}"] = {};
				datesDelivery[{$by_address@key}]["{$date@key}"]['minimal'] = ["{$date.0.0}",{$date.0.1}];
				datesDelivery[{$by_address@key}]["{$date@key}"]['maximal'] = ["{$date.1.0}",{$date.1.1}];
			{/if}
		{/foreach}
	{/foreach}
	{literal}

	function refreshDateOfDelivery()
	{
		var date_from = null;
		var date_to = null;
		var set = true;
		$.each($('.delivery_option_radio:checked'), function()
		{
			var date = datesDelivery[$(this).attr('name').replace(/delivery_option\[(.*)\]/, '$1')][$(this).val()];
			if (typeof(date) != 'undefined')
			{
				if (date_from == null || date_from[1] < date['minimal'][1])
					date_from = date['minimal'];
				if (date_to == null || date_to[1] < date['maximal'][1])
					date_to = date['maximal'];
			}
			else
				set = false;
		});

		if (date_from != null && date_to != null && set)
		{
			$('p#dateofdelivery').show();
			$('span#minimal').html('<b>'+date_from[0]+'</b>');
			$('span#maximal').html('<b>'+date_to[0]+'</b>');
		}
		else
			$('p#dateofdelivery').hide();
	}
	$(function(){
		refreshDateOfDelivery();
		$('input[name=id_carrier]').change(function(){
			refreshDateOfDelivery();
		});
	});
	{/literal}
	</script>

	<br />
	<p id="dateofdelivery">
		{if $nbPackages <= 1}
			{l s='Approximate date of delivery with this carrier is between' mod='dateofdelivery'}
		{else}
			{l s='There are %s packages, that will be approximately delivered with the delivery option you choose between' sprintf=$nbPackages mod='dateofdelivery'}
		{/if}
			<span id="minimal"></span> {l s='and' mod='dateofdelivery'} <span id="maximal"></span> <sup>*</sup>
		<br />
		<span style="font-size:10px;margin:0;padding:0;"><sup>*</sup> {l s='with direct payment methods (e.g. credit card)' mod='dateofdelivery'}
	</p>
{/if}