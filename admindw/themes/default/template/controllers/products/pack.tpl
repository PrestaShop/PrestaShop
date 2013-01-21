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
</script>
<input type="hidden" name="submitted_tabs[]" value="Pack" />
<h4>{l s='Pack'}</h4>
<div class="separation"></div>

<table>
	<tr>
		<td>
			<div class="ppack">
				<input type="checkbox" name="ppack" id="ppack" value="1" {if $product_type == Product::PTYPE_PACK}checked="checked"{/if} onclick="$('#ppackdiv').slideToggle();" />
				<label class="t" for="ppack">{l s='Pack'}</label>
			</div>
		</td>
		<td>
			<div id="ppackdiv" {if !($product_type == Product::PTYPE_PACK)}style="display: none;"{/if}>

				<label for="curPackItemName" style="width:560px;text-align:left;">
					{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}
				</label><br /><br />

				<input type="text" size="25" id="curPackItemName" />
				<input type="text" name="curPackItemQty" id="curPackItemQty" value="1" size="1" />
				<input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items}" />
				<input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items}" />
				<input type="hidden" size="2" id="curPackItemId" />

				<span id="add_pack_item" class="button" style="cursor: pointer;">
					{l s='Add this product to the pack'}
				</span>

				<p class="product_description listOfPack" style="display:{if count($pack_items) > 0}block{else}none{/if};text-align: left;">
					<br />{l s='List of products for that pack:'}
				</p>

				<div id="divPackItems">
					{foreach $pack_items as $pack_item}
						{$pack_item.pack_quantity} x {$pack_item.name}
						<span class="delPackItem" name="{$pack_item.id}" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" />
						</span><br />
					{/foreach}
				</div>

				<br />
				<p class="hint" style="display:block">{l s='You cannot add combinations to a pack.'}</p>

			</td>
		</div>
	</tr>
</table>
