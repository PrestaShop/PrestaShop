<div class="control-group">
	<label class="control-label">{l s='Limit to a single customer'}</label>
	<div class="controls">
		<input type="hidden" id="id_customer" name="id_customer" value="{$currentTab->getFieldValue($currentObject, 'id_customer')|intval}" />
		<input type="text" id="customerFilter" class="input-large" name="customerFilter" value="{$customerFilter|escape:'htmlall':'UTF-8'}" />
		<span class="help-block">{l s='Optional: The cart rule will be available to everyone if you leave this field blank.'}</span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">{l s='Valid'}</label>
	<div class="controls">
		<div class="input-prepend">
  			<span class="add-on">{l s='From'}</span>
			<input type="text" class="datepicker input-medium" name="date_from"
			value="{if $currentTab->getFieldValue($currentObject, 'date_from')}{$currentTab->getFieldValue($currentObject, 'date_from')|escape}{else}{$defaultDateFrom}{/if}" />
		</div>
		<div class="input-prepend">
			<span class="add-on">{l s='To'}</span>
			<input type="text" class="datepicker input-medium" name="date_to"
			value="{if $currentTab->getFieldValue($currentObject, 'date_to')}{$currentTab->getFieldValue($currentObject, 'date_to')|escape}{else}{$defaultDateTo}{/if}" />
		</div>
		<span class="help-block">{l s='The default period is one month.'}</span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">{l s='Minimum amount'}</label>
	<div class="controls">
		<input type="text" class="input-mini" name="minimum_amount" value="{$currentTab->getFieldValue($currentObject, 'minimum_amount')|floatval}" />
		<select name="minimum_amount_currency" class="input-small">
		{foreach from=$currencies item='currency'}
			<option value="{$currency.id_currency|intval}"
			{if $currentTab->getFieldValue($currentObject, 'minimum_amount_currency') == $currency.id_currency
				|| (!$currentTab->getFieldValue($currentObject, 'minimum_amount_currency') && $currency.id_currency == $defaultCurrency)}
				selected="selected"
			{/if}
			>
				{$currency.iso_code}
			</option>
		{/foreach}
		</select>
		<select name="minimum_amount_tax" class="input-medium">
			<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 0}selected="selected"{/if}>{l s='Tax excluded'}</option>
			<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 1}selected="selected"{/if}>{l s='Tax included'}</option>
		</select>
		<select name="minimum_amount_shipping" class="input-medium">
			<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 0}selected="selected"{/if}>{l s='Shipping excluded'}</option>
			<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 1}selected="selected"{/if}>{l s='Shipping included'}</option>
		</select>
		<span class="help-block">{l s='You can choose a minimum amount for the cart either with or without the taxes and shipping.'}</span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">{l s='Total available'}</label>
	<div class="controls">
		<input type="text" class="input-mini" name="quantity" value="{$currentTab->getFieldValue($currentObject, 'quantity')|intval}" />
		<span class="help-inline">{l s='The cart rule will be applied to the first "X" customers only.'}</span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">{l s='Total available for each user.'}</label>
	<div class="controls">
		<input type="text" class="input-mini" name="quantity_per_user" value="{$currentTab->getFieldValue($currentObject, 'quantity_per_user')|intval}" />
		<span class="help-inline">{l s='A customer will only be able to use the cart rule "X" time(s).'}</span>
	</div>
</div>


<div class="control-group">

	{if $countries.unselected|@count + $countries.selected|@count > 1}
	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="country_restriction" name="country_restriction" value="1" {if $countries.unselected|@count}checked="checked"{/if} />
			{l s='Country selection'}
		</label>
		<div id="country_restriction_div">
			<table>
				<tr>
					<td >
						<p><strong>{l s='Unselected countries'}</strong></p>
						<select id="country_select_1" class="input-large" multiple>
							{foreach from=$countries.unselected item='country'}
								<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
							{/foreach}
						</select>
						<a id="country_select_add" class="btn btn-block clearfix">{l s='Add'} <i class="icon-arrow-right"></i></a>
					</td>
					<td>
						<p><strong>{l s='Selected countries'}</strong></p>
						<select name="country_select[]" id="country_select_2" class="input-large" multiple>
							{foreach from=$countries.selected item='country'}
								<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
							{/foreach}
						</select>
						<a id="country_select_remove" class="btn btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove'} </a>
					</td>
				</tr>
			</table>
			<span class="help-block">{l s='This restriction applies to the country of delivery.'}</span>
		</div>
	</div>
	{/if}
	
	{if $carriers.unselected|@count + $carriers.selected|@count > 1}
	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="carrier_restriction" name="carrier_restriction" value="1" {if $carriers.unselected|@count}checked="checked"{/if} />
			{l s='Carrier selection'}
		</label>
		<div id="carrier_restriction_div">
			<table>
				<tr>
					<td>
						<p><strong>{l s='Unselected carriers'}</strong></p>
						<select id="carrier_select_1" class="input-large" multiple>
							{foreach from=$carriers.unselected item='carrier'}
								<option value="{$carrier.id_reference|intval}">&nbsp;{$carrier.name|escape}</option>
							{/foreach}
						</select>
						<a id="carrier_select_add" class="btn btn-block clearfix" >{l s='Add'} <i class="icon-arrow-right"></i></a>
					</td>
					<td>
						<p><strong>{l s='Selected carriers'}</strong></p>
						<select name="carrier_select[]" id="carrier_select_2" class="input-large" multiple>
							{foreach from=$carriers.selected item='carrier'}
								<option value="{$carrier.id_reference|intval}">&nbsp;{$carrier.name|escape}</option>
							{/foreach}
						</select>
						<a id="carrier_select_remove" class="btn btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove'} </a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	{/if}

	{if $groups.unselected|@count + $groups.selected|@count > 1}
	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="group_restriction" name="group_restriction" value="1" {if $groups.unselected|@count}checked="checked"{/if} />
			{l s='Customer group selection'}
		</label>
		<div id="group_restriction_div">
			<table>
				<tr>
					<td>
						<p><strong>{l s='Unselected groups'}</strong></p>
						<select id="group_select_1" class="input-large" multiple>
							{foreach from=$groups.unselected item='group'}
								<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
							{/foreach}
						</select>
						<a id="group_select_add" class="btn btn-block clearfix" >{l s='Add'} <i class="icon-arrow-right"></i></a>
					</td>
					<td>
						<p><strong>{l s='Selected groups'}</strong></p>
						<select name="group_select[]" class="input-large" id="group_select_2" multiple>
							{foreach from=$groups.selected item='group'}
								<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
							{/foreach}
						</select>
						<a id="group_select_remove" class="btn btn-block clearfix" ><i class="icon-arrow-left"></i> {l s='Remove'}</a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	{/if}

	{if $cart_rules.unselected|@count + $cart_rules.selected|@count > 0}
	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="cart_rule_restriction" name="cart_rule_restriction" value="1" {if $cart_rules.unselected|@count}checked="checked"{/if} />
			{l s='Compatibility with other cart rules'}
		</label>
		<div id="cart_rule_restriction_div" >
			<table>
				<tr>
					<td style="padding-left:20px;">
						<p><strong>{l s='Uncombinable cart rules'}</strong></p>
						<select id="cart_rule_select_1" class="input-large" multiple="">
							{foreach from=$cart_rules.unselected item='cart_rule'}
								<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name|escape}</option>
							{/foreach}
						</select>
						<a id="cart_rule_select_add" class="btn btn-block clearfix">{l s='Add'} <i class="icon-arrow-right"></i></a>
					</td>
					<td>
						<p><strong>{l s='Combinable cart rules'}</strong></p>
						<select name="cart_rule_select[]" class="input-large" id="cart_rule_select_2" multiple>
							{foreach from=$cart_rules.selected item='cart_rule'}
								<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name|escape}</option>
							{/foreach}
						</select>
						<a id="cart_rule_select_remove" class="btn btn-block clearfix" ><i class="icon-arrow-left"></i> {l s='Remove'}</a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	{/if}

	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="product_restriction" name="product_restriction" value="1" {if $product_rule_groups|@count}checked="checked"{/if} />
			{l s='Product selection'}
		</label>
		<div id="product_restriction_div">
			<table id="product_rule_group_table" cellpadding="0" cellspacing="0">
				{foreach from=$product_rule_groups item='product_rule_group'}
					{$product_rule_group}
				{/foreach}
			</table>
			<a href="javascript:addProductRuleGroup();" class="btn">
				<i class="icon-plus-sign"></i> {l s='Product selection'}
			</a>
		</div>
	</div>

	{if $shops.unselected|@count + $shops.selected|@count > 1}
	<div class="controls well">
		<label class="checkbox">
			<input type="checkbox" id="shop_restriction" name="shop_restriction" value="1" {if $shops.unselected|@count}checked="checked"{/if} />
			{l s='Shop selection'}
		</label>
		<div id="shop_restriction_div" >
			<table>
				<tr>
					<td>
						<p><strong>{l s='Unselected shops'}</strong></p>
						<select id="shop_select_1" multiple>
							{foreach from=$shops.unselected item='shop'}
								<option value="{$shop.id_shop|intval}">&nbsp;{$shop.name|escape}</option>
							{/foreach}
						</select>
						<a id="shop_select_add" class="btn" >{l s='Add'} &gt;&gt; </a>
					</td>
					<td>
						<p><strong>{l s='Selected shops'}</strong></p>
						<select name="shop_select[]" id="shop_select_2" multiple>
							{foreach from=$shops.selected item='shop'}
								<option value="{$shop.id_shop|intval}">&nbsp;{$shop.name|escape}</option>
							{/foreach}
						</select>
						<a id="shop_select_remove" class="btn" > &lt;&lt; {l s='Remove'} </a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	{/if}
	
</div>