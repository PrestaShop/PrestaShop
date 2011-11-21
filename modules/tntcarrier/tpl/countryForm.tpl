<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=country&action=edit" method="post" class="form" id="configFormCountry">
	<table class="table" cellspacing="0" cellpadding="0">
		<tr>
			<td><input type="hidden" name="tnt_carrier_country" size="20" value="{$varCountryForm.country}"/>{$varCountryForm.country}</td>
			<td><input type="text" name="tnt_carrier_{$varCountryForm.country}_overcost" size="20" value="{$varCountryForm.overcost}"/></td>
			<td><input class="button" name="submitSave" type="submit"></td>
		</tr>
	</table>
</form>