	<fieldset>
		<form action="{$formAction}" method="post">
			<input type="hidden" name="method" value="delivery_options"/>

			<h3>{l s='Shipping preferences' mod='dejala'}</h3>

				{include file="$timetableTpl"}

				<h3>{l s='Propose shipping ' mod='dejala'}:</h3>
				<div class="margin-form">
					<select name="delivery_delay">
						<option value="0" {if $delivery_delay == "0"}selected="selected"{/if}>{l s='immediately' mod='dejala'}</option>
						<option value="0.5" {if $delivery_delay == "0.5"}selected="selected"{/if}>{l s='half a day' mod='dejala'}</option>
						<option value="1" {if $delivery_delay == "1"}selected="selected"{/if}>{l s='a day' mod='dejala'}</option>
						<option value="2" {if $delivery_delay == "2"}selected="selected"{/if}>2 {l s='days' mod='dejala'}</option>
						<option value="3" {if $delivery_delay == "3"}selected="selected"{/if}>3 {l s='days' mod='dejala'}</option>
						<option value="4" {if $delivery_delay == "4"}selected="selected"{/if}>4 {l s='days' mod='dejala'}</option>
						<option value="5" {if $delivery_delay == "5"}selected="selected"{/if}>5 {l s='days' mod='dejala'}</option>

					</select>
					{l s=' after ordering' mod='dejala'}
				</div>

				<h3>{l s='Display ' mod='dejala'}:</h3>
				<div class="margin-form">
					<select name="nb_days">
						{section name=day start=1 loop=15 step=1}
						  <option value="{$smarty.section.day.index}" {if $nb_days == $smarty.section.day.index}selected="selected"{/if}>{$smarty.section.day.index} {l s='dates' mod='dejala'}</option>
						{/section}
					</select>
					{l s=' days in the timerange selection interface' mod='dejala'}
				</div>

				<label class="clear">{l s='Keep Dejala active when cart is out of stock' mod='dejala'}:</label>
				<div class="margin-form">
					<input type="checkbox" name="delivery_partial" value="1" {if ($delivery_partial)}checked="checked"{/if}>
					{l s='Enable customers to choose Dejala even if a product is out of stock (The customer will choose the date of the delivery when their order will be ready)' mod='dejala'}
				</div>

				<br/>
				<input type="submit" value="{l s='Save' mod='dejala'}" class="button" />			
		</form>
	</fieldset>
