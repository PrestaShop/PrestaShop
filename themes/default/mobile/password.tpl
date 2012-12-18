{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture assign='page_title'}{l s='Forgot your password?'}{/capture}
{include file='./page-title.tpl'}

{include file="$tpl_dir./errors.tpl"}
<div data-role="content" id="content">
	{if isset($confirmation) && $confirmation == 1}
	<p class="success">{l s='Your password has been successfully reset and a confirmation has been sent to your e-mail address:'} {$email|escape:'htmlall':'UTF-8'}</p>
	{elseif isset($confirmation) && $confirmation == 2}
	<p class="success">{l s='A confirmation e-mail has been sent to your address:'} {$email|escape:'htmlall':'UTF-8'}</p>
	{else}
	<p>{l s='Please enter the e-mail address used to register. We will send your new password to that address.'}</p>
	<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" id="form_forgotpassword">
		<fieldset>
			<label for="email">{l s='E-mail:'}</label>
			<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
			<input type="submit" class="button" data-theme="a" value="{l s='Retrieve Password'}" />
		</fieldset>
	</form>
	{/if}
	<p class="clear">
		<a href="{$link->getPageLink('authentication', true)}" title="{l s='Return to Login'}"><img src="{$img_dir}icon/my-account.gif" alt="{l s='Return to Login'}" class="icon" /></a><a href="{$link->getPageLink('authentication')}" title="{l s='Back to Login'}" data-ajax="false">{l s='Back to Login'}</a>
	</p>
</div><!-- /content -->
