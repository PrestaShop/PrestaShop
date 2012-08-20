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
*  @version  Release: $Revision: 11069 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<input type="hidden" name="warehouse_loaded" value="1">
{if isset($product->id)}
	<input type="hidden" name="submitted_tabs[]" value="Warehouses" />
	<h4>{l s='Product location in warehouses'}</h4>
				<div class="separation"></div>
				<div class="hint" style="display:block; position:'auto';">
		<p>{l s='This interface allows you to specify in which warehouses the product is stocked.'}</p>
		<p>{l s='It is also possible to specify for each product/product combination its location in each warehouse.'}</p>
	</div>
	<p>{l s='Please choose the warehouses associated with this product, and the default one.'}</p>

	<a class="button bt-icon confirm_leave" href="{$link->getAdminLink('AdminWarehouses')|escape:'htmlall':'UTF-8'}&addwarehouse">
		<img src="../img/admin/add.gif" alt="{l s='Create new warehouse'}" title="{l s='Create new warehouse'}" /><span>{l s='Create new warehouse'}</span>
	</a>

	<div id="warehouse_accordion" style="margin-top:10px; display:block;">
		{foreach from=$warehouses item=warehouse}
		    <h3 style="margin-bottom:0;"><a href="#">{$warehouse['name']}</a></h3>
		    <div style="display:block;">
				<table cellpadding="10" cellspacing="0" class="table">
					<tr>
						<th width="100">{l s='Stored'}</th>
						<th>{l s='Product'}</th>
						<th width="150">{l s='Location (optional)'}</th>
					</tr>
					{foreach $attributes AS $index => $attribute}
						{assign var=location value=''}
						{assign var=selected value=''}
						{foreach from=$associated_warehouses item=aw}
							{if $aw->id_product == $attribute['id_product'] && $aw->id_product_attribute == $attribute['id_product_attribute'] && $aw->id_warehouse == $warehouse['id_warehouse']}
								{assign var=location value=$aw->location}
								{assign var=selected value=true}
							{/if}
						{/foreach}
						<tr {if $index is odd}class="alt_row"{/if}>
							<td><input type="checkbox"
								name="check_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
								{if $selected == true}checked="checked"{/if}
								value="1" />
							</td>
							<td>{$product_designation[$attribute['id_product_attribute']]}</td>
							<td><input type="text"
								name="location_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
								value="{$location|escape:'htmlall':'UTF-8'}"
								size="20" />
							</td>
						</tr>
					{/foreach}
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					{if $attributes|@count gt 1}
					<tr>
						<td><input type="checkbox" class="check_all_warehouse" value="check_warehouse_{$warehouse['id_warehouse']}" /></td>
						<td colspan="2"><i>{l s='Mark all products as stored in this warehouse.'}</i></td>
					</tr>
					{/if}
				</table>
			</div>
		{/foreach}
	</div>
	<p>&nbsp;</p>
{/if}