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
*  @version  Release: $Revision: 8858 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	var there_are = '{l s='There are'}';
	var there_is = '{l s='There is'}';
	var label_errors = '{l s='errors'}';
	var label_error = '{l s='error'}';
</script>

<div id="container">	
	<div id="error" style="{if !isset($errors)}display:none;{/if}">
		{if isset($errors)}
			<h3>{if $nbErrors > 1}{l s='There are'}{else}{l s='There is'}{/if} {$nbErrors}{if $nbErrors > 1} {l s='errors'}{else} {l s='error'}{/if}</h3>
			<ol style="margin: 0 0 0 20px;">
			{foreach from=$errors item="error"}
				<li>{$error}</li>
			{/foreach}
			</ol>
		{/if}
	</div>
	<br />
	{if isset($warningSslMessage)}
		<div class="warn">
			{$warningSslMessage}
		</div>
	{/if}
	<div id="login">
	{if !isset($wrong_folder_name)}
		<h1>{$shop_name}</h1>
		<form action="" method="post" id="login_form" onsubmit="doAjaxLogin('{$redirect}');return false;">
			<label for="email">{l s='E-mail address:'}</label><br />
			<input type="text" id="email" name="email" class="input" value="{if isset($email)}{$email|htmlentities}{/if}" />
			<div style="margin: 1.8em 0 0 0;">
				<label for="passwd">{l s='Password:'}</label><br />
				<input id="passwd" type="password" name="passwd" class="input" value="{if isset($password)}{$password}{/if}"/>
			</div>
			<div>
				<div id="submit">
					<input type="hidden" name="redirect" value="{$redirect}"/>
					<input type="submit" name="submitLogin" value="{l s='Log in'}" class="button" style="float:left"/>
					<span style="float:left;width:30px">
						<img id="ajax-loader" src="../img/loader.gif" style="float:left;margin:2px 0 0 5px;display:none">
					</span>
				</div>
				<div id="lost">
					<a href="#" onclick="displayForgotPassword();return false;">{l s='Lost password?'}</a>
				</div>
			</div>
			<script type="text/javascript">
			//TODO FOCUS ON EMAIL
			</script>
		</form>
	{else}
		<h1>{$shop_name}</h1>
		<div style="margin:30px;">
			<p><span>{l s='For security reasons, you cannot connect to the Back Office until after you have:'}<br /><br />
				<ul>
					<li>{l s='delete the /install folder'}</li>
					<li>{l s='renamed the /admin folder (eg.) /admin'}{$randomNb}</li>
				</ul>
			<br />{l s='Please then access this page by the new url (eg.) http://www.domain.tld/admin'}{$randomNb}</span></p>
		</div>
	{/if}
	</div>
	<div id="forgot_password" style="display:none">
		<h1>{$shop_name}</h1>	
		<form action="" method="post" onsubmit="doAjaxForgot();return false;">
			<div class="page-title center">{l s='Forgot your password?'}</div><br />
			<span style="font-weight: bold;">{l s='Please, enter your e-mail address the one you wrote during your registration in order to receive your access codes by e-mail'}</span><br />
			<input style="margin-top:20px" type="text" name="email_forgot" id="email_forgot" class="input" />
			<div id="submit">
				<input type="submit" name="Submit" value="{l s='Send'}" class="button" style="float:left" />
				<span style="float:left;width:30px">
					<img id="ajax-loader" src="../img/loader.gif" style="float:left;margin:2px 0 0 5px;display:none">
				</span>
			</div>
			<div id="lost"><a href="#" onclick="displayLogin();return false;">{l s='Back to login'}</a></div>
		</form>
	</div>
	<h2><a href="http://www.prestashop.com">&copy; Copyright by PrestaShop. all rights reserved.</a></h2>
</div>
