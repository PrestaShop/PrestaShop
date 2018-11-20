{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="form-group">
	<label class="control-label  col-lg-3">{l s='Free shipping' d='Admin.Shipping.Feature'}</label>
	<div class="col-lg-9">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" name="free_shipping" id="free_shipping_on" value="1" {if $currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			<label class="t" for="free_shipping_on">
				{l s='Yes' d='Admin.Global'}
			</label>
			<input type="radio" name="free_shipping" id="free_shipping_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			<label class="t" for="free_shipping_off">
				{l s='No' d='Admin.Global'}
			</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">{l s='Apply a discount' d='Admin.Catalog.Feature'}</label>
	<div class="col-lg-9">
		<div class="radio">
			<label for="apply_discount_percent">
				<input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" {if $currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
				{l s='Percent (%)' d='Admin.Catalog.Feature'}
			</label>
		</div>
		<div class="radio">
			<label for="apply_discount_amount">
				<input type="radio" name="apply_discount" id="apply_discount_amount" value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0}checked="checked"{/if} />
				{l s='Amount' d='Admin.Global'}
			</label>
		</div>
		<div class="radio">
			<label for="apply_discount_off">
				<input type="radio" name="apply_discount" id="apply_discount_off" value="off" {if !$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0 && !$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
				<i class="icon-remove color_danger"></i> {l s='None' d='Admin.Global'}
			</label>
		</div>
	</div>
</div>

<div id="apply_discount_percent_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Value' d='Admin.Global'}</label>
	<div class="col-lg-9">
		<div class="input-group col-lg-2">
			<span class="input-group-addon">%</span>
			<input type="text" id="reduction_percent" class="input-mini" name="reduction_percent" value="{$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval}" />
		</div>
		<span class="help-block"><i class="icon-warning-sign"></i> {l s='Does not apply to the shipping costs' d='Admin.Catalog.Help'}</span>
	</div>
</div>

<div id="apply_discount_amount_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Amount' d='Admin.Global'}</label>
	<div class="col-lg-7">
		<div class="row">
			<div class="col-lg-4">
				<input type="text" id="reduction_amount" name="reduction_amount" value="{$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval}" onchange="this.value = this.value.replace(/,/g, '.');" />
			</div>
			<div class="col-lg-4">
				<select name="reduction_currency" >
				{foreach from=$currencies item='currency'}
					<option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'reduction_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'reduction_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>{$currency.iso_code}</option>
				{/foreach}
				</select>
			</div>
			<div class="col-lg-4">
				<select name="reduction_tax" >
					<option value="0" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 0}selected="selected"{/if}>{l s='Tax excluded' d='Admin.Global'}</option>
					<option value="1" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 1}selected="selected"{/if}>{l s='Tax included' d='Admin.Global'}</option>
				</select>
			</div>
		</div>
	</div>
</div>

<div id="apply_discount_to_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Apply a discount to' d='Admin.Catalog.Feature'}</label>
	<div class="col-lg-7">
		<p class="radio">
			<label for="apply_discount_to_order">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order"{if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == 0} checked="checked"{/if} />
				 {l s='Order (without shipping)' d='Admin.Catalog.Feature'}
			</label>
		</p>
		<p class="radio">
			<label for="apply_discount_to_product">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"{if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval > 0} checked="checked"{/if} />
				{l s='Specific product' d='Admin.Catalog.Feature'}
			</label>
		</p>
		<p class="radio">
			<label for="apply_discount_to_cheapest">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"{if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -1} checked="checked"{/if} />
				 {l s='Cheapest product' d='Admin.Catalog.Feature'}
			</label>
		</p>
		<p class="radio">
			<label for="apply_discount_to_selection">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"{if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -2} checked="checked"{/if}{if $product_rule_groups|@count == 0}disabled="disabled"{/if} />
				{l s='Selected product(s)' d='Admin.Catalog.Feature'}{if $product_rule_groups|@count == 0}&nbsp;<span id="apply_discount_to_selection_warning" class="text-muted clearfix"><i class="icon-warning-sign"></i> <a href="#" id="apply_discount_to_selection_shortcut">{l s='You must select some products before' d='Admin.Catalog.Notification'}</a></span>{/if}
			</label>
		</p>
	</div>
</div>

<div id="apply_discount_to_product_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Product' d='Admin.Global'}</label>
	<div class="col-lg-9">
		<div class="input-group col-lg-5">
			<input type="text" id="reductionProductFilter" name="reductionProductFilter" value="{$reductionProductFilter|escape:'html':'UTF-8'}" />
			<input type="hidden" id="reduction_product" name="reduction_product" value="{$currentTab->getFieldValue($currentObject, 'reduction_product')|intval}" />
			<span class="input-group-addon"><i class="icon-search"></i></span>
		</div>
	</div>
</div>

<div id="apply_discount_to_product_special" class="form-group">
 	<label class="control-label col-lg-3">
    <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='If enabled, the voucher will not apply to products already on sale.'}">
    {l s='Exclude discounted products' d='Admin.Catalog.Feature'}
    </span>
  </label>
 	<div class="col-lg-9">
 		<span class="switch prestashop-switch fixed-width-lg">
 			<input type="radio" name="reduction_exclude_special" id="reduction_exclude_special_on" value="1"{if $currentTab->getFieldValue($currentObject, 'reduction_exclude_special')|intval} checked="checked"{/if}/>
 			<label class="t" for="reduction_exclude_special_on">
 				{l s='Yes' d='Admin.Global'}
 			</label>
 			<input type="radio" name="reduction_exclude_special" id="reduction_exclude_special_off" value="0"{if !$currentTab->getFieldValue($currentObject, 'reduction_exclude_special')|intval} checked="checked"{/if}/>
 			<label class="t" for="reduction_exclude_special_off">
 				{l s='No' d='Admin.Global'}
 			</label>
 			<a class="slide-button btn"></a>
 		</span>
 	</div>
 </div>

<div class="form-group">
	<label class="control-label col-lg-3">{l s='Send a free gift' d='Admin.Catalog.Feature'}</label>
	<div class="col-lg-9">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" name="free_gift" id="free_gift_on" value="1" {if $currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			<label class="t" for="free_gift_on">
				{l s='Yes' d='Admin.Global'}
			</label>
			<input type="radio" name="free_gift" id="free_gift_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			<label class="t" for="free_gift_off">
				{l s='No' d='Admin.Global'}
			</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>

<div id="free_gift_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Search a product' d='Admin.Catalog.Feature'}</label>
	<div class="col-lg-9">
		<div class="input-group col-lg-5">
			<input type="text" id="giftProductFilter" value="{$giftProductFilter}" />
			<span class="input-group-addon"><i class="icon-search"></i></span>
		</div>
	</div>
</div>

<div id="gift_products_found" {if $gift_product_select == ''}style="display:none"{/if}>
	<div id="gift_product_list" class="form-group">
		<label class="control-label col-lg-3">{l s='Matching products' d='Admin.Catalog.Feature'}</label>
		<div class="col-lg-5">
			<select name="gift_product" id="gift_product" onclick="displayProductAttributes();" class="control-form">
				{$gift_product_select}
			</select>
		</div>
	</div>
	<div id="gift_attributes_list" class="form-group" {if !$hasAttribute}style="display:none"{/if}>
		<label class="control-label col-lg-3">{l s='Available combinations' d='Admin.Catalog.Feature'}</label>
		<div class="col-lg-5" id="gift_attributes_list_select">
			{$gift_product_attribute_select}
		</div>
	</div>
</div>
<div id="gift_products_err" class="alert alert-warning" style="display:none"></div>
