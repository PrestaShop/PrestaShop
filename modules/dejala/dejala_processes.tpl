		<form action="{$formAction}" method="post">
			<input type="hidden" name="method" value="processes"/>
			<fieldset>
				<h4 class="clear">{l s='Please specify the processes to properly handle deliveries' mod='dejala'}:</h4>
				{l s='Feel free to adapt the default processes to your own business processes' mod='dejala'}<br/>
				<br/>
				
				<label class="clear">{l s='Bad address' mod='dejala'}:</label>
				<div class="margin-form"><textarea rows="4" cols="45" name="process_bad_address">{$process_bad_address}</textarea></div>

				<label class="clear">{l s='Absent recipient' mod='dejala'}:</label>
				<div class="margin-form"><textarea rows="4" cols="45" name="process_absent_recipient">{$process_absent_recipient}</textarea></div>

				<label class="clear">{l s='Delivery rejected' mod='dejala'}:</label>
				<div class="margin-form"><textarea rows="4" cols="45" name="process_delivery_rejected">{$process_delivery_rejected}</textarea></div>
				
				<label class="clear">{l s='Delivery packaging' mod='dejala'}:</label>
				<div class="margin-form"><textarea rows="4" cols="45" name="process_packaging">{$process_packaging}</textarea></div>

				<div class="margin-form"><input type="submit" name="btnSubmit" value="{l s='Update settings' mod='dejala'}" class="button" /></div>
			</fieldset>
		</form>
	