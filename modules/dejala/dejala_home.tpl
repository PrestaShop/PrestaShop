{literal}
<script type="text/javascript">
<!--
function toggleVisibleUserListState(state) {
	var elt = document.getElementById('visible_users_list');

	if (state == 0) {
		elt.style.setProperty('background-color', '#ECE9D8', '') ;
		elt.style.setProperty('color', '#ACA899', '') ;
		elt.setAttribute('disabled', 'disabled') ;
	}
	else {
		elt.style.removeProperty('background-color') ;
		elt.style.removeProperty('color') ;
		elt.removeAttribute('disabled');
	}
}

function updateDJLVisibilityChoices() {
	for (var i=0 ; i < document.visibility_form.visibility_status.length ; i++) {
		var elt = document.visibility_form.visibility_status[i] ;
		if (elt.checked) {
			elt.parentNode.style.setProperty('color', '#993300', '') ;
			elt.parentNode.style.setProperty('font-weight', 'bold', '') ;
		}
		else {
			elt.parentNode.style.removeProperty('color') ;
			elt.parentNode.style.removeProperty('font-weight') ;
		}
		if (elt.value == "visible_limited") {
			toggleVisibleUserListState(elt.checked) ;
		}
	}
}

addLoadEvent(function() {
	for (var i=0 ; i < document.visibility_form.visibility_status.length ; i++) {
		document.visibility_form.visibility_status[i].onclick = function() { updateDJLVisibilityChoices() ;} ;
	}
	updateDJLVisibilityChoices() ;
}) ;

//-->
</script>
{/literal}

	{if (isset($registered) AND $registered == 0)}

		<div style="width:100%;margin:0px 10px 0px 10px">
			<div class="clear"></div>
			<div style="float:left;width:48%;">
				<form action="{$formAction}" method="post">
					<input type="hidden" name="method" value="register">
					<fieldset >
						<legend>{l s='New to Dejala.fr ?' mod='dejala'}</legend>
						<div>
							{l s='Your shop name:' mod='dejala'} <input size="30" type="text" name="store_name" value="{$store_name}"/>
						</div>
						<div>
							{l s='Select your country:' mod='dejala'}
							<select name="country">
								<option value="fr">{l s='France' mod='dejala'}</option>
								<option value="es">{l s='Spain' mod='dejala'}</option>
							</select>
						</div>
						<div>
							{l s='Choose your login:' mod='dejala'} <input size="30" type="text" name="login" value="{$login}"/>
						</div>
						<div>
							{l s='Choose your password:' mod='dejala'} <input size="15" type="password" name="password" value=""/>
						</div>
						<br/>
						<input type="submit" name="btnSubmit" value="{l s='Register' mod='dejala'}" class="button" {$disabled} />
					</fieldset>
				</form>
			</div>

			<div style="float:left; margin-left:5px;width:47%;">
				<form action="{$formAction}" method="post">
					<input type="hidden" name="method" value="signin" />
					<fieldset>
						<legend>{l s='I already have an account for my shop:' mod='dejala'}</legend>
						<div>{l s='Login:' mod='dejala'} <input size="30" type="text" name="login" value="{$login}"/></div>
						<div>
							{l s='Select your country:' mod='dejala'}
							<select name="country">
								<option value="fr">{l s='France' mod='dejala'}</option>
								<option value="es">{l s='Spain' mod='dejala'}</option>
							</select>
						</div>
						<div>{l s='Password:' mod='dejala'} <input size="15" type="password" name="password" value=""/></div>
						<br/>
						<input type="submit" name="btnSubmit" value="{l s='Sign-in' mod='dejala'}" class="button" {$disabled} />
					</fieldset>
				</form>
			</div>
		</div>
		<div class="clear"></div>

		{else}
			<fieldset>
			<div id="dejalaAutopub" style="float:right;margin:0px;margin-top:10px;">
				<iframe frameborder="no" scrolling="no" style="margin:0px; margin-top:-10px; padding: 0px; width: 310px; height: 270px;" src="http://module.pro.dejala.{$country}/tabs/home_pub.php"></iframe>
			</div>
			<div style="width:65%;">
				<legend>{l s='Dejala.fr' mod='dejala'}</legend>
				{l s='As a reminder' mod='dejala'} :<br/>
				{l s='Your store name is' mod='dejala'} : {$store_name}<br/>
				{l s='Your login is' mod='dejala'} : {if isset($store_login)}{$store_login}{/if}<br/><br/>
				{l s='Your are running on the' mod='dejala'} : {if ($djl_mode=='TEST')}{l s='test platform' mod='dejala'}{else}{l s='production platform' mod='dejala'}{/if}<br/>

				{* Switch mode button : switch between test/prod modes  *}
				{if ($djl_mode=='PROD')}
					<form action="{$formAction}" method="post">
						<input type="hidden" name="method" value="switchMode">
						<input type="hidden" name="mode" value="TEST">
						<input type="submit" name="btnSubmit" value="{l s='Switch to test mode' mod='dejala'}" class="button" />
					</form>
				{else}
					{if (isset($isLiveReady) AND $isLiveReady=='1')}
						<form action="{$formAction}" method="post">
							<input type="hidden" name="method" value="switchMode">
							<input type="hidden" name="mode" value="PROD">
							<input type="submit" name="btnSubmit" value="{l s='Switch to production mode' mod='dejala'}" class="button" />
						</form>
					{else}
						{if (isset($isLiveReady) AND $isLiveRequested=='1')}
							{l s='Your request to go live is under process : Dejala.fr will contact you to finalize your registration.' mod='dejala'}
						{else}
							{* Demande de passage en prod *}
							<form action="{$formAction}" method="post">
								<input type="hidden" name="method" value="golive">
								<input type="submit" name="btnSubmit" value="{l s='Go live : request Dejala.fr to create my account in production.' mod='dejala'}" class="button" />
							</form>
						{/if}
					{/if}
				{/if}

				<br/><br/>
				{if ($djl_mode == 'PROD')}{l s='Your credit' mod='dejala'} :{else if ($djl_mode == 'TEST')}{l s='Your virtual credit (in order to test)' mod='dejala'} :{/if} {$account_balance} {l s='euros' mod='dejala'}<br/>
				{if ($djl_mode == 'PROD')}<a href="http://pro.dejala.{$country}" target="_blank" style="color:blue;font-weight:bold;text-decoration:underline;">{l s='Credit your account' mod='dejala'}</a><br/>{/if}

				<br/><br/>
				<form action="{$formAction}" method="post" name="visibility_form">
					<div>
					<input type="radio" name="visibility_status" value="invisible" {if ($visibility_status == "invisible")}{"checked=\"checked\""}{/if} /> {l s='Dejala IS NOT visible for any users' mod='dejala'}
					</div>
					<div>
					<input type="radio" name="visibility_status" value="visible" {if ($visibility_status == "visible")}{"checked=\"checked\""}{/if} /> {l s='Dejala IS visible for all users' mod='dejala'}
					</div>
					<div>
					<input type="radio" name="visibility_status" value="visible_limited" {if ($visibility_status == "visible_limited")}{"checked=\"checked\""}{/if} /> {l s='Dejala IS visible ONLY for the following users' mod='dejala'} :
					</div>
					<div>
					<input size="40" type="text" id="visible_users_list" name="visible_users_list" value="{if isset($visible_users_list)}{$visible_users_list}{/if}"/> {l s='(e.g. : a@foo.com, b@bar.com)' mod='dejala'}
					</div>
					<input type="hidden" name="method" value="switchActive">
					<br />
					<input type="submit" name="btnSubmit" value="{l s='Update Dejala visibility' mod='dejala'}" class="button" />
				</form>
			</div>


			</fieldset>
		{/if}

