{if isset($display_error)}
	{if $display_error}
		<div class="error">{l s='An error occured during the form validation'}</div>
	{else}
		<div class="conf">{l s='Configuration updated'}</div>
	{/if}
{/if}

<form method="post" action="{$smarty.server.REQUEST_URI}">
	<fieldset>
		<legend>{l s='Global Configuration'}</legend>
		
		<label for="refresh_method">Refresh carrier list method</label>
		<div class="margin-form">
			<select id="refresh_method" name="refresh_method">
				<option value="0" {if $refresh_method == 0}selected{/if}>{l s='Anytime'}</option>
				<option value="1" {if $refresh_method == 1}selected{/if}>{l s='Required all information set'}</option>
			</select>
			<p>{l s='Set the way to refresh information for a customer'}</p>
		</div>
		
		<div class="margin-form">
			<input name="setGlobalConfiguration" type="submit" class="button" value="{l s='Submit'}">
		</div>
	</fieldset>
</form>