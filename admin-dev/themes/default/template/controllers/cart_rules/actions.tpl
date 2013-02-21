<label>{l s='Free shipping'}</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="free_shipping" id="free_shipping_on" value="1" {if $currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
	<label class="t" for="free_shipping_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /></label>
	&nbsp;&nbsp;
	<input type="radio" name="free_shipping" id="free_shipping_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'free_shipping')|intval}checked="checked"{/if} />
	<label class="t" for="free_shipping_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /></label>
</div>
<hr />
<label>{l s='Apply a discount'}</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" {if $currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
	<label class="t" for="apply_discount_percent"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /> {l s='Percent (%)'}</label>
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_amount" value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0}checked="checked"{/if} />
	<label class="t" for="apply_discount_amount"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /> {l s='Amount'}</label>
	&nbsp;&nbsp;
	<input type="radio" name="apply_discount" id="apply_discount_off" value="off" {if !$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval > 0 && !$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval > 0}checked="checked"{/if} />
	<label class="t" for="apply_discount_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /> {l s='None'}</label>
</div>
<div id="apply_discount_percent_div">
	<label>{l s='Value'}</label>
	<div class="margin-form">
		<input type="text" id="reduction_percent" name="reduction_percent" value="{$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval}" style="width:40px" /> %
		<p>{l s='Does not apply to the shipping costs'}</p>
	</div>
</div>
<div id="apply_discount_amount_div">
	<label>{l s='Amount'}</label>
	<div class="margin-form">
		<input type="text" id="reduction_amount" name="reduction_amount" value="{$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval}" onchange="this.value = this.value.replace(/,/g, '.');" />
		<select name="reduction_currency">
		{foreach from=$currencies item='currency'}
			<option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'reduction_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'reduction_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>{$currency.iso_code}</option>
		{/foreach}
		</select>
		<select name="reduction_tax">
			<option value="0" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 0}selected="selected"{/if}>{l s='Tax excluded'}</option>
			<option value="1" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 1}selected="selected"{/if}>{l s='Tax included'}</option>
		</select>
	</div>
</div>
<div id="apply_discount_to_div">
	<label>{l s='Apply a discount to'}</label>
	<div class="margin-form">
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order" {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == 0}checked="checked"{/if} />
		<label class="t" for="apply_discount_to_order"> {l s='Order (without shipping)'}</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval > 0}checked="checked"{/if} />
		<label class="t" for="apply_discount_to_product"> {l s='Specific product'}</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -1}checked="checked"{/if} />
		<label class="t" for="apply_discount_to_cheapest"> {l s='Cheapest product'}</label>
		&nbsp;&nbsp;
		<input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -2}checked="checked"{/if} />
		<label class="t" for="apply_discount_to_selection"> {l s='Selected product(s)'}</label>
	</div>
	<div id="apply_discount_to_product_div">
		<label>{l s='Product'}</label>
		<div class="margin-form">
			<input type="hidden" id="reduction_product" name="reduction_product" value="{$currentTab->getFieldValue($currentObject, 'reduction_product')|intval}" />
			<input type="text" id="reductionProductFilter" name="reductionProductFilter" value="{$reductionProductFilter|escape:'htmlall':'UTF-8'}" style="width:400px" />
		</div>
	</div>
</div>
<hr />
<label>{l s='Send a free gift'}</label>
<div class="margin-form">
	&nbsp;&nbsp;
	<input type="radio" name="free_gift" id="free_gift_on" value="1" {if $currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
	<label class="t" for="free_gift_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /></label>
	&nbsp;&nbsp;
	<input type="radio" name="free_gift" id="free_gift_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'gift_product')|intval}checked="checked"{/if} />
	<label class="t" for="free_gift_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /></label>
</div>
<div id="free_gift_div">
	<label>{l s='Search a product'}</label>
	<div class="margin-form">
		<input type="text" id="giftProductFilter" value="{$giftProductFilter}" style="width:400px" />
	</div>
	<div id="gift_products_found" {if $gift_product_select == ''}style="display:none"{/if}>
		<div id="gift_product_list">
			<label>{l s='Matching products'}</label>
			<select name="gift_product" id="gift_product" onclick="displayProductAttributes();">
				{$gift_product_select}
			</select>
		</div>
		<div class="clear">&nbsp;</div>
		<div id="gift_attributes_list" {if !$hasAttribute}style="display:none"{/if}>
			<label>{l s='Available combinations'}</label>
			<div id="gift_attributes_list_select">
				{$gift_product_attribute_select}
			</div>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
	<div id="gift_products_err" class="warn" style="display:none"></div>
</div>
