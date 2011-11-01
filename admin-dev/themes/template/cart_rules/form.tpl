<form action="{$currentIndex}&token={$currentToken}&addcart_rule" id="cart_rule_form" method="post">
	<fieldset>
		<legend><img src="../img/t/AdminCartRules.gif" /> {l s='Cart Rule'}</legend>
		{if $currentObject->id}<input type="hidden" name="id_cart_rule" value="{$currentObject->id|intval}" />{/if}
		<label>{l s='Name'}</label>
		<div class="margin-form">
		
		{foreach from=$languages item=language}
			<input type="text" name="name_{$language.id_lang}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang)}" style="width:300px" />
			<br />
		{/foreach}
			<p>{l s='Will be displayed in the cart summary as well as on the invoice.'}</p>
		</div>
		<label>{l s='Description'}</label>
		<div class="margin-form">
			<textarea name="description" style="width:90%;height:100px">{$currentTab->getFieldValue($currentObject, 'description')}</textarea>
			<p>{l s='For you only, never displayed to the customer.'}</p>
		</div>
		<label>{l s='Code'}</label>
		<div class="margin-form">
			<input type="text" id="code" name="code" value="{$currentTab->getFieldValue($currentObject, 'code')}" />
			<img src="../img/admin/news-new.gif" onclick="gencode(8);" style="cursor:pointer" />
			<p>{l s='Optional, the rule will automatically be applied if you leave this field blank.'}</p>
		</div>
		<label>{l s='Partial use'}</label>
		<div class="margin-form">
			<select name="partial_use">
				<option value="0" {if $currentTab->getFieldValue($currentObject, 'partial_use') == 0}selected="selected"{/if}>{l s='No - Lower the voucher value to the total order amount'}</option>
				<option value="1" {if $currentTab->getFieldValue($currentObject, 'partial_use') == 1}selected="selected"{/if}>{l s='Yes - Create a new voucher with the remainder'}</option>
			</select>
		</div>
		<label>{l s='Priority'}</label>
		<div class="margin-form">
			<input type="text" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
		</div>
		<label>{l s='Status'}</label>
		<div class="margin-form">
			&nbsp;&nbsp;
			<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /></label>
			&nbsp;&nbsp;
			<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /></label>
		</div>
		<hr />
		<legend style="margin:-22px 0 10px 0"><img src="../img/admin/access.png" /> {l s='Conditions'}</legend>
		{include file='cart_rules/conditions.tpl'}
		<div class="clear">&nbsp;</div><hr />
		<legend style="margin:-22px 0 10px 0"><img src="../img/admin/asterisk.gif" /> {l s='Actions'}</legend>
		{include file='cart_rules/actions.tpl'}
		<hr />
		<input type="submit" value="{l s='Save'}" class="button" name="submitAddcart_rule" />
	</fieldset>
</form>
<script type="text/javascript">
	var product_rules_counter = {if isset($product_rules_counter)}{$product_rules_counter}{else}0{/if};
	var currentToken = '{$currentToken}';
</script>
<script type="text/javascript" src="themes/template/cart_rules/form.js"></script>