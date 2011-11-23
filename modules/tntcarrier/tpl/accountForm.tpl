<p>{l s='The following parameters were provided to you by TNT' mod='tntcarrier'}. {l s='If you are not yet registered, click ' mod='tntcarrier'} <a style="color:blue;text-decoration:underline" href="https://www.tnt.fr/public/utilisateurs/adminExt/new.do">{l s='here' mod='tntcarrier'}</a></p>
	<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=1&section=account" method="post" class="form" id="configFormAccount">
		<fieldset style="border: 0px;">
			<h4>{l s='Account TNT' mod='tntcarrier'} :</h4>
			<label>{l s='Login' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_login" value="{$varAccount.login}" /></div>
			<label>{l s='Password' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="password" size="20" name="tnt_carrier_password" value="{$varAccount.password}" /></div>
			<label>{l s='Number account' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_number_account" value="{$varAccount.account}" /></div>
		</fieldset>
	<div class="margin-form"><input class="button" name="submitSave" type="submit" value="{l s='save' mod='tntcarrier'}"></div>
</form>
