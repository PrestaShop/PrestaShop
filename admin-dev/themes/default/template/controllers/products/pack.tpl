{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 11204 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">

	var msg_select_one = '{l s='Please select at least one product.' js=1}';
	var msg_set_quantity = '{l s='Please set a quantity to add a product.' js=1}';

	$(document).ready(function() {
		if ($('#ppack').attr('checked'))
		{
			$('#ppack').attr('disabled', 'disabled');
			$('#ppackdiv').show();
		}

		$('div.ppack').hide();

		$('#curPackItemName').autocomplete('ajax_products_list.php', {
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
			},
			extraParams: {
				excludeIds : getSelectedIds(),
				excludeVirtuals : 1
			}
		}).result(function(event, item){
			$('#curPackItemId').val(item[1]);
		});

	});

	function addPackItem()
	{
		var curPackItemId = $('#curPackItemId').val();
		var curPackItemName = $('#curPackItemName').val();
		var curPackItemQty = $('#curPackItemQty').val();
		if (curPackItemId == '' || curPackItemName == '')
		{
			jAlert(msg_select_one);
			return false;
		}
		else if (curPackItemId == '' || curPackItemQty == '')
		{
			jAlert(msg_set_quantity);
			return false;
		}

		var lineDisplay = curPackItemQty+ 'x ' +curPackItemName;

		var divContent = $('#divPackItems').html();
		divContent += lineDisplay;
		divContent += '<span onclick="delPackItem(' + curPackItemId + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';

		// QTYxID-QTYxID
		// @todo : it should be better to create input for each items and each qty
		// instead of only one separated by x, - and ¤
		var line = curPackItemQty+ 'x' +curPackItemId;

		$('#inputPackItems').val($('#inputPackItems').val() + line  + '-');
		$('#divPackItems').html(divContent);
			$('#namePackItems').val($('#namePackItems').val() + lineDisplay + '¤');

		$('#curPackItemId').val('');
		$('#curPackItemName').val('');
		$('p.listOfPack').show();

		$('#curPackItemName').setOptions({
			extraParams: {
				excludeIds :  getSelectedIds()
			}
		});
		// show / hide save buttons
		// if product has a name
		handleSaveButtons();
	}

	function delPackItem(id)
	{
		var reg = new RegExp('-', 'g');
		var regx = new RegExp('x', 'g');

		var div = getE('divPackItems');
		var input = getE('inputPackItems');
		var name = getE('namePackItems');
		var select = getE('curPackItemId');
		var select_quantity = getE('curPackItemQty');

		var inputCut = input.value.split(reg);
		var nameCut = name.value.split(new RegExp('¤', 'g'));

		input.value = '';
		name.value = '';
		div.innerHTML = '';

		for (var i = 0; i < inputCut.length; ++i)
			if (inputCut[i])
			{
				var inputQty = inputCut[i].split(regx);
				if (inputQty[1] != id)
				{
					input.value += inputCut[i] + '-';
					name.value += nameCut[i] + '¤';
					div.innerHTML += nameCut[i] + ' <span onclick="delPackItem(' + inputQty[1] + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
				}
			}

		$('#curPackItemName').setOptions({
			extraParams: {
				excludeIds :  getSelectedIds()
			}
		});

		// if no item left in the pack, disable save buttons
		handleSaveButtons();
	}

	function getSelectedIds()
	{
		var ids = '';
		if (typeof(id_product) != 'undefined')
			ids += id_product + ',';
		ids += $('#inputPackItems').val().replace(/\d*x/g, '').replace(/\-/g,',');
		ids = ids.replace(/\,$/,'');
		return ids;
	}

</script>

<h4>{l s='Pack'}</h4>
<div class="separation"></div>

<table>
	<tr>
		<td>
			<div class="ppack">
				<input type="checkbox" name="ppack" id="ppack" value="1" {if $is_pack}checked="checked"{/if} onclick="$('#ppackdiv').slideToggle();" />
				<label class="t" for="ppack">{l s='Pack'}</label>
			</div>
		</td>
		<td>
			<div id="ppackdiv" {if !$is_pack}style="display: none;"{/if}>

				<label for="curPackItemName" style="width:560px;text-align:left;">
					{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}
				</label><br /><br />

				<input type="text" size="25" id="curPackItemName" />
				<input type="text" name="curPackItemQty" id="curPackItemQty" value="1" size="1" />
				<input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items}" />
				<input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items}" />
				<input type="hidden" size="2" id="curPackItemId" />

				<span onclick="addPackItem();" class="button" style="cursor: pointer;">
					{l s='Add this product to the pack'}
				</span>

				<p class="product_description listOfPack" style="display:{if count($product->packItems) > 0}block{else}none{/if};text-align: left;">
					<br />{l s='List of products for that pack:'}
				</p>

				<div id="divPackItems">
					{foreach from=$product->packItems item=packItem}
						{$packItem->pack_quantity} x {$packItem->name}
						<span onclick="delPackItem({$packItem->id});" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" />
						</span><br />
					{/foreach}
				</div>

				<br />
				<p class="hint" style="display:block">{l s='You cannot add downloadable products to a pack.'}</p>

			</td>
		</div>
	</tr>
</table>
