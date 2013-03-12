<div data-role="content" id="content">
	
	<form action="{$link->getPageLink('authentication', true)}" method="post" id="create-account_form" class="std login_form" data-ajax="false">
		<h2>{l s='Create an account'}</h2>
		<div class="form_content clearfix">
			<p class="title_block">{l s='Enter your email address to create an account'}.</p>
			<fieldset>
				<span><input type="email" id="email_create" placeholder="{l s='Email address'}" name="email_create" value="{if isset($smarty.post.email_create)}{$smarty.post.email_create|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" /></span>
			</fieldset>
			{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
			<button type="submit" id="SubmitCreate" name="SubmitCreate" class="ui-btn-hidden submit_button" aria-disabled="false" data-theme="a">{l s='Create an account'}</button>
			<input type="hidden" class="hidden" name="SubmitCreate" value="{l s='Create an account'}" />
		</div>
	</form>

	<hr width="99%" align="center" size="2" class=""/>

	<form action="{$link->getPageLink('authentication', true)}" method="post" class="login_form">
		<h2>{l s='Already registered?'}</h2>
		<fieldset>
			<input type="email" id="email" name="email" placeholder="{l s='Email address'}" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" />
		</fieldset>
		
		<fieldset>
			<input type="password" id="passwd" name="passwd" placeholder="{l s='Password'}" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="account_input" />
			<p class="forget_pwd"><a href="{$link->getPageLink('password')}" data-ajax="false">{l s='Forgot your password?'}</a></p>
		</fieldset>
		<button type="submit" class="ui-btn-hidden submit_button" id="SubmitLogin" name="SubmitLogin" aria-disabled="false" data-theme="a">{l s='Login'}</button>
	</form> 
</div><!-- /content -->

{* Missing the guest checkout behaviour *}
{* ===================================== *}
