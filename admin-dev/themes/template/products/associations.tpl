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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">

	$(document).ready(function() {
		$('input').keypress(function(e) { 
			var code = null; 
			code = (e.keyCode ? e.keyCode : e.which);
			return (code == 13) ? false : true;
		});
	});

</script>

<div class="Associations">
	<h4>{l s='Product price'}</h4>
	<div class="separation"></div>
		<div id="no_default_category" style="font-weight: bold;display: none;">
		{l s='Please check a category in order to select the default category.'}
	</div>
	<table cellspacing="0" cellpadding="0" border="0">
	 <tr>
			<td class="col-left">
			<label for="id_category_default">{l s='Default category:'}</label>
			</td>
			<td class="col-right">
	<select id="id_category_default" name="id_category_default">
	{foreach from=$selected_cat item=cat}
		<option value="{$cat.id_category}" {if $product->id_category_default == $cat.id_category}selected="selected"{/if} >{$cat.name}</option>
	{/foreach}
	</select>
	</td>
	</tr>
	</table>
	<br/>
	<div id="category_block">{$category_tree}</div>
	{if $feature_shop_active}
		{* @todo use asso_shop from Helper *}
		<tr id="shop_association">
			<td class="col-left"><label>{l s='Shop association:'}</label></td>
			<td style="padding-bottom:5px;">{$displayAssoShop}</td>
		</tr>
	{/if}

<div class="separation"></div>
	<table>
		<tr>
			<td class="col-left"><label>{l s='Accessories:'}<br /><br /><i>{l s='(Do not forget to Save the product afterward)'}</i></label></td>
			<td style="padding-bottom:5px;">
				<input type="hidden" name="inputAccessories" id="inputAccessories" value="{foreach from=$accessories item=accessory}{$accessory.id_product}-{/foreach}" />
				<input type="hidden" name="nameAccessories" id="nameAccessories" value="{foreach from=$accessories item=accessory}{$accessory.name|htmlentitiesUTF8}¤{/foreach}" />

				<div id="ajax_choose_product" style="padding:6px; padding-top:2px; width:600px;">
					<p style="clear:both;margin-top:0;" class="preference_description">
						{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}
						<input type="text" value="" id="product_autocomplete_input" />
					</p>
					
					<!--<img onclick="$(this).prev().search();" style="cursor: pointer;" src="../img/admin/add.gif" alt="{l s='Add an accessory'}" title="{l s='Add an accessory'}" />-->
				</div>
				<div id="divAccessories">
					{* @todo : donot use 3 foreach, but assign var *}
					{foreach from=$accessories item=accessory}
						{$accessory.name|htmlentitiesUTF8}{if !empty($accessory.reference)}{$accessory.reference}{/if} 
						<span onclick="delAccessory({$accessory.id_product});" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" class="middle" alt="" />
						</span><br />
					{/foreach}
				</div>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
	var formProduct;
	var accessories = new Array();
	urlToCall = null;
	/* function autocomplete */
	$(document).ready(function() {
		$('#product_autocomplete_input')
			.autocomplete('ajax_products_list.php', {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result(addAccessory);
		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : getAccessorieIds()
			}
		});
	});

	function getAccessorieIds()
	{
		var ids = {$product->id}+',';
		ids += $('#inputAccessories').val().replace(/\\-/g,',').replace(/\\,$/,'');
		ids = ids.replace(/\,$/,'');

		return ids;
	}
</script>