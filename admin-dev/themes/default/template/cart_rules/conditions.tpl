<label>{l s='Limit to a single customer'}</label>
<div class="margin-form">
	<input type="hidden" id="id_customer" name="id_customer" value="{$currentTab->getFieldValue($currentObject, 'id_customer')|intval}" />
	<input type="text" id="customerFilter" name="customerFilter" value="{$customerFilter}" style="width:400px" />
	<p>{l s='Optional, the cart rule will be available for everyone if you leave this field blank.'}</p>
</div>
<label>{l s='Validity'}</label>
<div class="margin-form">
	<strong>{l s='from'}</strong>
	<input type="text" class="datepicker" name="date_from" value="{if $currentTab->getFieldValue($currentObject, 'date_from')}{$currentTab->getFieldValue($currentObject, 'date_from')}{else}{$defaultDateFrom}{/if}" />
	<strong>{l s='to'}</strong>
	<input type="text" class="datepicker" name="date_to" value="{if $currentTab->getFieldValue($currentObject, 'date_to')}{$currentTab->getFieldValue($currentObject, 'date_to')}{else}{$defaultDateTo}{/if}" />
	<p>{l s='Default period is one year.'}</p>
</div>
<label>{l s='Minimum amount'}</label>
<div class="margin-form">
	<input type="text" name="minimum_amount" value="{$currentTab->getFieldValue($currentObject, 'minimum_amount')|floatval}" />
	<select name="minimum_amount_currency">
	{foreach from=$currencies item='currency'}
		<option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'minimum_amount_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>{$currency.iso_code}</option>
	{/foreach}
	</select>
	<select name="minimum_amount_tax">
		<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 0}selected="selected"{/if}>{l s='Tax excluded'}</option>
		<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 1}selected="selected"{/if}>{l s='Tax included'}</option>
	</select>
	<select name="minimum_amount_shipping">
		<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 0}selected="selected"{/if}>{l s='Shipping excluded'}</option>
		<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 1}selected="selected"{/if}>{l s='Shipping included'}</option>
	</select>
	<p>{l s='You can choose a minimum amount for the cart either with or without the taxes, with or without shipping.'}</p>
</div>
<label>{l s='Total available'}</label>
<div class="margin-form">
	<input type="text" name="quantity" value="{$currentTab->getFieldValue($currentObject, 'quantity')|intval}" />
</div>
<label>{l s='Total available for each user'}</label>
<div class="margin-form">
	<input type="text" name="quantity_per_user" value="{$currentTab->getFieldValue($currentObject, 'quantity_per_user')|intval}" />
</div>
{if $countries.unselected|@count + $countries.selected|@count > 1}
<br />
<input type="checkbox" id="country_restriction" name="country_restriction" value="1" {if $countries.unselected|@count}checked="checked"{/if} /> <strong>{l s='Country selection'}</strong>
<div id="country_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td>
				<p><strong>{l s='Selected countries'}</strong></p>
				<select name="country_select[]" id="country_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$countries.selected item='country'}
						<option value="{$country.id_country|intval}">&nbsp;{$country.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="country_select_remove">
					{l s='Remove'} &gt;&gt;
				</a>
			</td>
			<td style="padding-left:20px;">
				<p><strong>{l s='Unselected countries'}</strong></p>
				<select id="country_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$countries.unselected item='country'}
						<option value="{$country.id_country|intval}">&nbsp;{$country.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="country_select_add">
					&lt;&lt; {l s='Add'}
				</a>
			</td>
		</tr>
	</table>
</div>
{/if}
{if $carriers.unselected|@count + $carriers.selected|@count > 1}
<br />
<input type="checkbox" id="carrier_restriction" name="carrier_restriction" value="1" {if $carriers.unselected|@count}checked="checked"{/if} /> <strong>{l s='Carrier selection'}</strong>
<div id="carrier_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td>
				<p><strong>{l s='Selected carriers'}</strong></p>
				<select name="carrier_select[]" id="carrier_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$carriers.selected item='carrier'}
						<option value="{$carrier.id_carrier|intval}">&nbsp;{$carrier.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="carrier_select_remove">
					{l s='Remove'} &gt;&gt;
				</a>
			</td>
			<td style="padding-left:20px;">
				<p><strong>{l s='Unselected carriers'}</strong></p>
				<select id="carrier_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$carriers.unselected item='carrier'}
						<option value="{$carrier.id_carrier|intval}">&nbsp;{$carrier.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="carrier_select_add">
					&lt;&lt; {l s='Add'}
				</a>
			</td>
		</tr>
	</table>
</div>
{/if}
{if $groups.unselected|@count + $groups.selected|@count > 1}
<br />
<input type="checkbox" id="group_restriction" name="group_restriction" value="1" {if $groups.unselected|@count}checked="checked"{/if} /> <strong>{l s='Customer group selection'}</strong>
<div id="group_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td>
				<p><strong>{l s='Selected groups'}</strong></p>
				<select name="group_select[]" id="group_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$groups.selected item='group'}
						<option value="{$group.id_group|intval}">&nbsp;{$group.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="group_select_remove">
					{l s='Remove'} &gt;&gt;
				</a>
			</td>
			<td style="padding-left:20px;">
				<p><strong>{l s='Unselected groups'}</strong></p>
				<select id="group_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$groups.unselected item='group'}
						<option value="{$group.id_group|intval}">&nbsp;{$group.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="group_select_add">
					&lt;&lt; {l s='Add'}
				</a>
			</td>
		</tr>
	</table>
</div>
{/if}
{if $cart_rules.unselected|@count + $cart_rules.selected|@count > 0}
<br />
<input type="checkbox" id="cart_rule_restriction" name="cart_rule_restriction" value="1" {if $cart_rules.unselected|@count}checked="checked"{/if} /> <strong>{l s='Other cart rules compatibility'}</strong>
<div id="cart_rule_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:0 10px 10px 10px;background-color:#FFF5D3">
	<table>
		<tr>
			<td>
				<p><strong>{l s='Combinable cart rules'}</strong></p>
				<select name="cart_rule_select[]" id="cart_rule_select_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
					{foreach from=$cart_rules.selected item='cart_rule'}
						<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="cart_rule_select_remove">
					{l s='Remove'} &gt;&gt;
				</a>
			</td>
			<td style="padding-left:20px;">
				<p><strong>{l s='Uncombinable cart rules'}</strong></p>
				<select id="cart_rule_select_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple="">
					{foreach from=$cart_rules.unselected item='cart_rule'}
						<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name}</option>
					{/foreach}
				</select><br /><br />
				<a style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="cart_rule_select_add">
					&lt;&lt; {l s='Add'}
				</a>
			</td>
		</tr>
	</table>
</div>
{/if}
<br />
<input type="checkbox" id="product_restriction" name="product_restriction" value="1" {if $product_rules|@count}checked="checked"{/if} /> <strong>{l s='Product selection'}</strong>
<div id="product_restriction_div" style="border:1px solid #AAAAAA;margin-top:10px;padding:10px 10px 10px 10px;background-color:#FFF5D3">
	{l s='Add a filter on'}
	<select id="product_rule_type">
		<option value="">{l s='-- Choose --'}</option>
		<option value="products">{l s='Products'}</option>
		<option value="attributes">{l s='Attributes'}</option>
		<option value="categories">{l s='Categories'}</option>
	</select>
	<a href="javascript:addProductRule();">
		<img src="../img/admin/add.gif" alt="{l s='Add'}" title="{l s='Add'}" /> {l s='Add'}
	</a>
	<hr />
	<table id="product_rule_table" class="table" cellpadding="0" cellspacing="0">
		{foreach from=$product_rules item='product_rule'}
			{$product_rule}
		{/foreach}
	</table>
</div>