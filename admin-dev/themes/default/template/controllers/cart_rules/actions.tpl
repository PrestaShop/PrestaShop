<div class="row">
	<label class="control-label  col-lg-3">{l s='Free shipping'}</label>
	<div class="input-group col-lg-2">
		<span class="switch prestashop-switch">
			<input type="radio" name="free_shipping" id="free_shipping_on" value="1" {if $currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			<label class="t radio" for="free_shipping_on">
			 	<i class="icon-check-sign"></i> {l s='Yes'}
			</label>
			<input type="radio" name="free_shipping" id="free_shipping_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			<label class="t radio" for="free_shipping_off">
				<i class="icon-ban-circle"></i> {l s='No'}
			</label>
			<span class="slide-button btn btn-default"></span>
		</span>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3">{l s='Apply a discount'}</label>
	<div class="input-group col-lg-8">
		<span class="switch prestashop-switch switch-three">
			<input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" {if $currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
			<label class="t radio" for="apply_discount_percent">
				 <i class="icon-check-sign"></i> {l s='Percent (%)'}
			</label>
			<input type="radio" name="apply_discount" id="apply_discount_amount" value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0}checked="checked"{/if} />
			<label class="t radio" for="apply_discount_amount">
				 <i class="icon-check-sign"></i> {l s='Amount'}
			</label>
			<input type="radio" name="apply_discount" id="apply_discount_off" value="off" {if !$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0 && !$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
			<label class="t radio" for="apply_discount_off">
				 <i class="icon-ban-circle"></i> {l s='None'}
			</label>
			<span class="slide-button btn btn-default"></span>
		</span>
	</div>
</div>

<div id="apply_discount_percent_div" class="row">
	<label class="control-label col-lg-3">{l s='Value'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">%</span>
		<input type="text" id="reduction_percent" class="input-mini" name="reduction_percent" value="{$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval}" />
	</div>
	<span class="help-block"><i class="icon-warning-sign"></i> {l s='Does not apply to the shipping costs'}</span>
</div>

<div id="apply_discount_amount_div" class="row">
	<label class="control-label col-lg-3">{l s='Amount'}</label>
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
					<option value="0" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 0}selected="selected"{/if}>{l s='Tax excluded'}</option>
					<option value="1" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 1}selected="selected"{/if}>{l s='Tax included'}</option>
				</select>
			</div>
		</div>
	</div>
</div>

<div id="apply_discount_to_div" class="row">
	<label class="control-label col-lg-3">{l s='Apply a discount to'}</label>
	<div class="col-lg-7">
		<p class="checkbox">
			<label class="t radio" for="apply_discount_to_order">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order" {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == 0}checked="checked"{/if} />
				 {l s='Order (without shipping)'}
			</label>
		</p>
		<p class="checkbox">
			<label class="t radio" for="apply_discount_to_product">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval > 0}checked="checked"{/if} />
				{l s='Specific product'}
			</label>
		</p>
		<p class="checkbox">
			<label class="t radio" for="apply_discount_to_cheapest">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -1}checked="checked"{/if} />
				 {l s='Cheapest product'}
			</label>
		</p>
		<p class="checkbox">
			<label class="t radio" for="apply_discount_to_selection">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -2}checked="checked"{/if} />
				{l s='Selected product(s)'}
			</label>
		</p>
	</div>
</div>

<div id="apply_discount_to_product_div" class="row">
	<label class="control-label col-lg-3">{l s='Product'}</label>
	<div class="input-group col-lg-5">
		<input type="text" id="reductionProductFilter" name="reductionProductFilter" value="{$reductionProductFilter|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" id="reduction_product" name="reduction_product" value="{$currentTab->getFieldValue($currentObject, 'reduction_product')|intval}" />
		<span class="input-group-addon"><i class="icon-search"></i></span>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3">{l s='Send a free gift'}</label>
	<div class="input-group col-lg-2">
		<span class="switch prestashop-switch">
			<input type="radio" name="free_gift" id="free_gift_on" value="1" {if $currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			<label class="t radio" for="free_gift_on">
				<i class="icon-check-sign"></i> {l s='Yes'}
			</label>
			<input type="radio" name="free_gift" id="free_gift_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			<label class="t radio" for="free_gift_off">
				<i class="icon-ban-circle"></i> {l s='No'}
			</label>
			<span class="slide-button btn btn-default"></span>
		</span>
	</div>
</div>

<div id="free_gift_div" class="row">
	<label class="control-label col-lg-3">{l s='Search a product'}</label>
	<div class="input-group col-lg-5">
		<input type="text" id="giftProductFilter" value="{$giftProductFilter}" />
		<span class="input-group-addon"><i class="icon-search"></i></span>
	</div>
</div>

<div id="gift_products_found" {if $gift_product_select == ''}style="display:none"{/if}>
	<br/>
	<div id="gift_product_list" class="row" >
		<label class="control-label col-lg-3">{l s='Matching products'}</label>
		<div class="col-lg-5">
			<select name="gift_product" id="gift_product" onclick="displayProductAttributes();">
				{$gift_product_select}
			</select>
		</div>
	</div>
	<div id="gift_attributes_list" class="row" {if !$hasAttribute}style="display:none"{/if}>
		<label class="control-label col-lg-3">{l s='Available combinations'}</label>
		<div class="col-lg-5" id="gift_attributes_list_select">
			{$gift_product_attribute_select}
		</div>
	</div>
</div>
<div id="gift_products_err" class="alert alert-block" style="display:none"></div>
