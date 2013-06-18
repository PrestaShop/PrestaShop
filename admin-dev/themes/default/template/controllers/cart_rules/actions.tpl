<div class="control-group well">
	<label class="control-label">{l s='Free shipping'}</label>
	<div class="controls">
		<label class="t radio" for="free_shipping_on">
			<input type="radio" name="free_shipping" id="free_shipping_on" value="1" {if $currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			 <i class="icon-check-sign"></i> {l s='Yes'}
		</label>
		<label class="t radio" for="free_shipping_off"> 
			<input type="radio" name="free_shipping" id="free_shipping_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
			<i class="icon-ban-circle"></i> {l s='No'}
		</label>
	</div>
</div>

<div class="control-group well">
	<label class="control-label">{l s='Apply a discount'}</label>
	<div class="controls">
		<label class="t radio" for="apply_discount_percent">
			<input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" {if $currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
			 <i class="icon-check-sign"></i> {l s='Percent (%)'}
		</label>
		<label class="t radio" for="apply_discount_amount">
			<input type="radio" name="apply_discount" id="apply_discount_amount" value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0}checked="checked"{/if} />
			 <i class="icon-check-sign"></i> {l s='Amount'}
		</label>
		<label class="t radio" for="apply_discount_off">
			<input type="radio" name="apply_discount" id="apply_discount_off" value="off" {if !$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0 && !$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
			 <i class="icon-ban-circle"></i> {l s='None'}
		</label>
	</div>


	<div id="apply_discount_percent_div">
		<label class="control-label">{l s='Value'}</label>
		<div class="controls">
			<input type="text" id="reduction_percent" class="input-mini" name="reduction_percent" value="{$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval}" /> %
			<span class="help-block">{l s='Does not apply to the shipping costs'}</span>
		</div>
	</div>

	<div id="apply_discount_amount_div">
		<label class="control-label">{l s='Amount'}</label>
		<div class="controls">
			<input type="text" id="reduction_amount" class="input-mini" name="reduction_amount" value="{$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval}" onchange="this.value = this.value.replace(/,/g, '.');" />
			<select name="reduction_currency" class="input-small">
			{foreach from=$currencies item='currency'}
				<option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'reduction_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'reduction_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>{$currency.iso_code}</option>
			{/foreach}
			</select>
			<select name="reduction_tax" class="input-medium">
				<option value="0" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 0}selected="selected"{/if}>{l s='Tax excluded'}</option>
				<option value="1" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 1}selected="selected"{/if}>{l s='Tax included'}</option>
			</select>
		</div>
	</div>

	<div id="apply_discount_to_div">
		<label class="control-label">{l s='Apply a discount to'}</label>
		<div class="controls">

			<label class="t radio" for="apply_discount_to_order">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order" {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == 0}checked="checked"{/if} />
				 {l s='Order (without shipping)'}
			</label>

			<label class="t radio" for="apply_discount_to_product">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval > 0}checked="checked"{/if} />
				{l s='Specific product'}
			</label>

			<label class="t radio" for="apply_discount_to_cheapest">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -1}checked="checked"{/if} />
				 {l s='Cheapest product'}
			</label>

			<label class="t radio" for="apply_discount_to_selection">
				<input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -2}checked="checked"{/if} />
				{l s='Selected product(s)'}
			</label>
		</div>

		<div id="apply_discount_to_product_div">
			<label class="control-label">{l s='Product'}</label>
			<div class="controls">
				<input type="hidden" id="reduction_product" name="reduction_product" value="{$currentTab->getFieldValue($currentObject, 'reduction_product')|intval}" />
				<input type="text" id="reductionProductFilter" class="input-large" name="reductionProductFilter" value="{$reductionProductFilter|escape:'htmlall':'UTF-8'}" />
			</div>
		</div>
	</div>
</div>


<div class="control-group well">
	<label class="control-label">{l s='Send a free gift'}</label>
	<div class="controls">
		<label class="t radio" for="free_gift_on">
			<input type="radio" name="free_gift" id="free_gift_on" value="1" {if $currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			 <i class="icon-check-sign"></i> {l s='Yes'}
		</label>
		<label class="t radio" for="free_gift_off">
			<input type="radio" name="free_gift" id="free_gift_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
			<i class="icon-ban-circle"></i> {l s='No'}
		</label>
	</div>

	<div id="free_gift_div">
		<label class="control-label">{l s='Search a product'}</label>
		<div class="controls">
			<input type="text" id="giftProductFilter" class="input-large" value="{$giftProductFilter}" />
		</div>
		<div id="gift_products_found" {if $gift_product_select == ''}style="display:none"{/if}>
			<div id="gift_product_list">
				<label>{l s='Matching products'}</label>
				<select name="gift_product" id="gift_product" onclick="displayProductAttributes();">
					{$gift_product_select}
				</select>
			</div>
			<div id="gift_attributes_list" {if !$hasAttribute}style="display:none"{/if}>
				<label>{l s='Available combinations'}</label>
				<div id="gift_attributes_list_select">
					{$gift_product_attribute_select}
				</div>
			</div>
		</div>
		<div id="gift_products_err" class="alert alert-block" style="display:none"></div>
	</div>
</div>