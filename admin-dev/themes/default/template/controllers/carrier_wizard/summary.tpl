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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2013 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var summary_translation_undefined = '{l s='[undefined]' js=1}';
	
	var summary_translation_meta_informations = '{l s='This carrier is @s1 and the displayed delivery time is: @s2.' js=1}';
	var summary_translation_free = '<strong>{l s='free' js=1}</strong>';
	var summary_translation_paid = '<strong>{l s='paid' js=1}</strong>';
	
	var summary_translation_range = '{l s='This carrier can deliver orders from @s1 to @s2. If the order is out of range, the behavior is to @s3.' js=1}';
	
	var summary_translation_shipping_cost = '{l s='The shipping cost is calculated @s1 and the tax rule @s2 is applied.' js=1}';
	var summary_translation_price = '<strong>{l s='according to the price' js=1}</strong>';
	var summary_translation_weight = '<strong>{l s='according to the weight' js=1}</strong>';
</script>

<fieldset>
	{l s='Carrier name:'} <strong id="summary_name"></strong>
	<div class="clear">&nbsp;</div>
	<div id="summary_meta_informations"></div>
	<div class="clear">&nbsp;</div>
	<div id="summary_shipping_cost"></div>
	<div class="clear">&nbsp;</div>
	<div id="summary_range"></div>
	<div class="clear">&nbsp;</div>
	<div>
		{l s='It will be displayed only for the following zones:'}
		<ul id="summary_zones"></ul>
	</div>
	<div class="clear">&nbsp;</div>
	<div>
		{l s='It will be displayed only for the following groups:'}
		<ul id="summary_groups"></ul>
	</div>
	{if $is_multishop}
	<div class="clear">&nbsp;</div>
	<div>
		{l s='It will be displayed only for the following shops:'}
		<ul id="summary_shops"></ul>
	</div>
	{/if}
</fieldset>
