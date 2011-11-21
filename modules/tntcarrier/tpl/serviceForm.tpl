<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=new" method="post" class="form" id="configFormService">
	{if $varServiceForm.id != null}
		<input type="hidden" name="service_id" value="{$varServiceForm.id}"/>
	{/if}
	<table class="table" cellspacing="0" cellpadding="0">
		<tr>
			<th>{$lang.name}</th><th>{$lang.description}</th><th>{$lang.code}</th><th>{$lang.additionnalCharge}</th><th>{$lang.activated}</th><th></th>
		</tr>
		<tr>
			<td><input type="text" name="tnt_carrier_service_name" size="20" value="{$varServiceForm.name}"/></td>
			<td><input type="text" name="tnt_carrier_service_description" size="20" value="{$varServiceForm.description}"/></td>
			<td><input type="text" name="tnt_carrier_service_code" size="5" value="{$varServiceForm.code}"/></td>
			<td><input type="text" name="tnt_carrier_service_charge" size="10" value="{$varServiceForm.charge}"/></td>
			<td><input type="radio" name="tnt_carrier_service_display" value="0" {if $varServiceForm.display == '1'} checked="checked"	{/if} /> <img src="../img/admin/disabled.gif" /><br/>
				<input type="radio" name="tnt_carrier_service_display" value="1" {if $varServiceForm.display == '0'} checked="checked"	{/if} /> <img src="../img/admin/enabled.gif" />
			</td>
			<td><input class="button" name="submitSave" type="submit"></td>
		</tr>
	</table>
</form>