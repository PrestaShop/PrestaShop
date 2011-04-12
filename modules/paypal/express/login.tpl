{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h2>{l s='Please log in' mod='paypal'}</h2>

{assign var='current_step' value='login'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

<form action="{$base_dir_ssl}modules/paypal/express/submit.php" method="post" id="login_form" class="std">
	<fieldset>
		<h3>{l s='This email has already been registered, please log in !' mod='paypal'}</h3>
		<p class="text">
			<label for="email" style="text-align:left; margin-left:10px;">{l s='E-mail address' mod='paypal'}</label>
			<span><input type="text" id="email" name="email" value="{$email|escape:'htmlall'|stripslashes}" class="account_input" /></span>
		</p>
		<p class="text">
			<label for="passwd" style="text-align:left; margin-left:10px;">{l s='Password' mod='paypal'}</label>
			<span><input type="password" id="passwd" name="passwd" value="{$passwd|escape:'htmlall'|stripslashes}" class="account_input" /></span>
		</p>
		<p class="submit" style="padding-top:15px;">
			<input type="hidden" name="token" value="{$ppToken|escape:'htmlall'|stripslashes}" />
			<input type="hidden" name="payerID" value="{$payerID|escape:'htmlall'|stripslashes}" />
			<input type="submit" id="submitLogin" name="submitLogin" class="button" value="{l s='Log in' mod='paypal'}" />
		</p>
		<p class="lost_password center"><a href="{$link->getPageLink('password.php')}">{l s='Forgot your password?' mod='paypal'}</a></p>
	</fieldset>
</form>
