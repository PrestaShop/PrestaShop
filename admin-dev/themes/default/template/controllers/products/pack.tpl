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

<script type="text/javascript">
	var msg_select_one = "{l s='Please select at least one product.' js=1}";
	var msg_set_quantity = "{l s='Please set a quantity to add a product.' js=1}";
</script>
<input type="hidden" name="submitted_tabs[]" value="Pack" />
<h3>{l s='Pack'}</h3>
<div class="alert alert-info">{l s='You cannot add combinations to a pack.'}</div>

<div class="ppack">
	<input type="checkbox" name="ppack" id="ppack" value="1" {if $product_type == Product::PTYPE_PACK}checked="checked"{/if} onclick="$('#ppackdiv').slideToggle();" />
	<label class="t" for="ppack">{l s='Pack'}</label>
</div>

<div id="ppackdiv" {if !($product_type == Product::PTYPE_PACK)}style="display: none;"{/if}>

<div class="row">
	<label class="control-label col-lg-3" for="curPackItemName">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Begin typing the first letters of the product name. Then select the product from the drop-down list:'}">
			{l s='Product'}
		</span>
	</label>
	<div class="input-group col-lg-6">
		<input type="text" id="curPackItemName" />
		<span class="input-group-addon"><i class="icon-search"></i></span>
	</div>
</div>
	
<div class="row">
	<label class="control-label col-lg-3" for="curPackItemQty">
		{l s='Quantity'}
	</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">&times;</span>
		<input type="text" name="curPackItemQty" id="curPackItemQty" value="1"/>
	</div>
</div>

<div class="row">
	<div class="col-lg-9 col-lg-offset-3">
		<button type="button" id="add_pack_item" class="btn btn-default">
			<i class="icon-plus-sign-alt"></i> {l s='Add this product to the pack'}
		</button>	
	</div>
</div>

<input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items}" />
<input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items}" />
<input type="hidden" id="curPackItemId" />

<hr/>

<div class="row">
	<label class="control-label col-lg-3 product_description listOfPack" style="display:{if count($pack_items) > 0}block{else}none{/if};">
		{l s='List of products for that pack:'}
	</label>
	<div class="col-lg-9">
		<ul id="divPackItems">
			{foreach $pack_items as $pack_item}
			<li>
				<button type="button" class="btn btn-default delPackItem" name="{$pack_item.id}">
					<i class="icon-trash"></i>
				</button>
				{$pack_item.pack_quantity} x {$pack_item.name}
			</li>
			{/foreach}
		</ul>
	</div>
</div>

