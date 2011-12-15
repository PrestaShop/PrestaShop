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
*  @version  Release: $Revision: 11204 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">

	var msg_select_one = '{l s='Thanks to select at least one product.'}';
	var msg_set_quantity = '{l s='Thanks to set a quantity to add a product.'}';

	$(document).ready(function() {

		$('input').keypress(function(e) { 
			var code = null; 
			code = (e.keyCode ? e.keyCode : e.which);
			return (code == 13) ? false : true;
		});

		$('#curPackItemName') .autocomplete('ajax_products_list.php', {
			delay: 100,
			minChars: 1,
			autoFill: true,
			max:20,
			matchContains: true,
			mustMatch:true,
			scroll:false,
			cacheLength:0,
			// param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete
			multipleSeparator:'||',
			formatItem: function(item) {
				return item[1]+' - '+item[0];
			}
		}).result(function(event, item){
			$('#curPackItemId').val(item[1]);
		});

		$('#curPackItemName').setOptions({
			extraParams: {
				excludeIds : getSelectedIds(), excludeVirtuals : 1
			}
		});

	});

</script>

<h4>{l s='Pack'}</h4>
<div class="separation"></div>

<table>
	<tr>
		<td>
			<input type="checkbox" name="ppack" id="ppack" value="1" {if $is_pack}checked="checked"{/if} onclick="$('#ppackdiv').slideToggle();" />
			<label class="t" for="ppack">{l s='Pack'}</label>
		</td>
		<td>
			<div id="ppackdiv" {if !$is_pack}style="display: none;"{/if}>
				<div id="divPackItems">
				{foreach from=$product->packItems item=packItem}
					{$packItem->pack_quantity} x {$packItem->name}<span onclick="delPackItem({$packItem->id});" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />
				{/foreach}
				</div>
				<input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items}" />

				<input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items}" />

				<input type="hidden" size="2" id="curPackItemId" />

				<p class="clear">{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}
				<br />{l s='You cannot add downloadable products to a pack.'}</p>
				<input type="text" size="25" id="curPackItemName" />
				<input type="text" name="curPackItemQty" id="curPackItemQty" value="1" size="1" />
				<span onclick="addPackItem();" style="cursor: pointer;"><img src="../img/admin/add.gif" alt="{l s='Add an item to the pack'}" title="{l s='Add an item to the pack'}" /></span>
			</td>
		</div>
	</tr>
</table>
