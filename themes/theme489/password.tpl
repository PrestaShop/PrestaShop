{capture name=path}{l s='Forgot your password?'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='Forgot your password?'}</h1>
{include file="$tpl_dir./errors.tpl"}
{if isset($confirmation) && $confirmation == 1}
<p class="success">{l s='Your password has been successfully reset and a confirmation has been sent to your e-mail address:'} {$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}</p>
{elseif isset($confirmation) && $confirmation == 2}
<p class="success">{l s='A confirmation e-mail has been sent to your address:'} {$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}</p>
{else}
<p>{l s='Please enter the e-mail address used to register. We will send your new password to that address.'}</p>
<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std forgot_password">
	<fieldset>
	<p id="restore_password" class="text">
			<label for="email">{l s='E-mail:'}</label>
			<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
		</p>
		<p class="submit">
			<input type="submit" class="button_large" value="{l s='Retrieve Password'}" />
		</p>
        		<div class="clearblock"></div>
	</fieldset>
</form>
{/if}
<ul class="footer_links">
	<li><a href="{$link->getPageLink('authentication', true)}" title="{l s='Return to Login'}"><img src="{$img_dir}icon/my-account.png" alt="{l s='Return to Login'}" class="icon" /></a><a href="{$link->getPageLink('authentication')}" title="{l s='Back to Login'}">{l s='Back to Login'}</a></li>
</ul>
