<form action="{$formAction}" method="post">
	<div style="float:left">
		<input type="hidden" name="method" value="contact">
		<fieldset style="width:250px;">
				<legend><img src="../img/t/AdminCustomers.gif" />{l s='Shop owner' mod='dejala'}</legend>
					<p>{l s='Name:' mod='dejala'} <input type="text" name="contact_owner_name" id="contact_owner_name" value="{$contact_owner_name|default:''}"></p>
					<p>{l s='Firstname:' mod='dejala'} <input type="text" name="contact_owner_firstname" id="contact_owner_firstname" value="{$contact_owner_firstname|default:''}"></p>
					<p>{l s='Phone:' mod='dejala'} <input type="text" name="contact_owner_phone" id="contact_owner_phone" value="{$contact_owner_phone|default:''}"></p>
					<p>{l s='Cellphone:' mod='dejala'} <input type="text" name="contact_owner_cellphone" id="contact_owner_cellphone" value="{$contact_owner_cellphone|default:''}"></p>
					<p>{l s='E-Mail:' mod='dejala'} <input type="text" name="contact_owner_email" id="contact_owner_email" value="{$contact_owner_email|default:''}"></p>
		</fieldset>

		<fieldset class="clear space" style="width:250px;">
			<legend><img src="../img/t/AdminCustomers.gif" />{l s='Stock Control Contact' mod='dejala'}</legend>
				<p>{l s='Name:' mod='dejala'} <input type="text" name="contact_stock_name" id="contact_stock_name" value="{$contact_stock_name|default:''}"></p>
				<p>{l s='Firstname:' mod='dejala'} <input type="text" name="contact_stock_firstname" id="contact_stock_firstname" value="{$contact_stock_firstname|default:''}"></p>
				<p>{l s='Phone:' mod='dejala'} <input type="text" name="contact_stock_phone" id="contact_stock_phone" value="{$contact_stock_phone|default:''}"></p>
				<p>{l s='Cellphone:' mod='dejala'} <input type="text" name="contact_stock_cellphone" id="contact_stock_cellphone" value="{$contact_stock_cellphone|default:''}"></p>
				<p>{l s='E-Mail:' mod='dejala'} <input type="text" name="contact_stock_email" id="contact_stock_email" value="{$contact_stock_email|default:''}"></p>
		</fieldset>
</div>
<div style="float:left; margin-left:5px;">
		<fieldset style="width:250px;">
			<legend><img src="../img/t/AdminCustomers.gif" />{l s='Administration Contact' mod='dejala'}</legend>
				<p>{l s='Name:' mod='dejala'} <input type="text" name="contact_administrative_name" id="contact_administrative_name" value="{$contact_administrative_name|default:''}"></p>
				<p>{l s='Firstname:' mod='dejala'} <input type="text" name="contact_administrative_firstname" id="contact_administrative_firstname" value="{$contact_administrative_firstname|default:''}"></p>
				<p>{l s='Phone:' mod='dejala'} <input type="text" name="contact_administrative_phone" id="contact_administrative_phone" value="{$contact_administrative_phone|default:''}"></p>
				<p>{l s='Cellphone:' mod='dejala'} <input type="text" name="contact_administrative_cellphone" id="contact_administrative_cellphone" value="{$contact_administrative_cellphone|default:''}"></p>
				<p>{l s='E-Mail:' mod='dejala'} <input type="text" name="contact_administrative_email" id="contact_administrative_email" value="{$contact_administrative_email|default:''}"></p>
		</fieldset>

		<fieldset class="clear space" style="width:250px">
			<legend><img src="../img/t/AdminCustomers.gif" />{l s='Website Maintenance Contact' mod='dejala'}</legend>
				<p>{l s='Name:' mod='dejala'} <input type="text" name="contact_web_name" id="contact_web_name" value="{$contact_web_name|default:''}"></p>
				<p>{l s='Firstname:' mod='dejala'} <input type="text" name="contact_web_firstname" id="contact_web_firstname" value="{$contact_web_firstname|default:''}"></p>
				<p>{l s='Phone:' mod='dejala'} <input type="text" name="contact_web_phone" id="contact_web_phone" value="{$contact_web_phone|default:''}"></p>
				<p>{l s='Cellphone:' mod='dejala'} <input type="text" name="contact_web_cellphone" id="contact_web_cellphone" value="{$contact_web_cellphone|default:''}"></p>
				<p>{l s='E-Mail:' mod='dejala'} <input type="text" name="contact_web_email" id="contact_web_email" value="{$contact_web_email|default:''}"></p>
			</fieldset>
</div>
<div class="clear"></div>
<br/>
<input type="submit" value="{l s='Save' mod='dejala'}" class="button" />

</form>

