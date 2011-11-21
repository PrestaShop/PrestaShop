<!--<div id="ajaxAnswer" style="float:right;text-align:center;width:50%;margin-top:100px"></div>-->
<p>{$lang.followParameters}. {$lang.registered} <a style="color:blue;text-decoration:underline" href="https://www.tnt.fr/public/utilisateurs/adminExt/new.do">{$lang.here}</a></p>
	<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=1&section=account" method="post" class="form" id="configFormAccount">
		<fieldset style="border: 0px;">
			<h4>{$lang.accountTNT} :</h4>
			<label>{$lang.login} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_login" value="{$varAccount.login}" /></div>
			<label>{$lang.password} : </label>
			<div class="margin-form"><input type="password" size="20" name="tnt_carrier_password" value="{$varAccount.password}" /></div>
			<label>{$lang.numberAccount} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_number_account" value="{$varAccount.account}" /></div>
		</fieldset>
	<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
</form>