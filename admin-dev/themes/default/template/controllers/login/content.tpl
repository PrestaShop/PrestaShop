{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
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
	<div class="row">
		<div id="login" class="col-md-4 col-md-offset-4 panel">
			<h1 class="text-center">{$shop_name}</h1>
			<hr/>
			<div id="error" class="hide alert alert-danger">
			{if isset($errors)}
				<h4>
					{if $nbErrors > 1}
						{l s='There are %d errors.' sprintf=$nbErrors}
					{else}{l s='There is %d error.' sprintf=$nbErrors}
					{/if}
				</h4>
				<ol>
					{foreach from=$errors item="error"}
					<li>{$error}</li>
					{/foreach}
				</ol>
				{/if}
			</div>
			{if isset($warningSslMessage)}
			<div class="alert alert-warning">{$warningSslMessage}</div>
			{/if}

			{if !isset($wrong_folder_name) && !isset($wrong_install_name)}
			<form action="#" id="login_form" method="post">
				<h3 class="text-center"><i class="icon-unlock"></i> {l s='Log in'}</h3>
				<div class="form-group">
					<label for="email">{l s='Email address:'}</label>
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-envelope"></i></span>
						<input
							name="email"
							type="text"
							id="email"
							class="email_field form-control"
							value="{if isset($email)}{$email|escape:'htmlall':'UTF-8'}{/if}"
							autofocus="autofocus"
							placeholder="test@example.com" />
					</div>
				</div>
				<div class="form-group">
					<label for="passwd">{l s='Password:'}</label>
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-key"></i></span>
						<input
							name="passwd"
							type="password"
							id="passwd"
							class="password_field form-control"
							value="{if isset($password)}{$password}{/if}"
							placeholder="{l s='Password'}" />
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<button class="btn btn-link show-forgot-password" type="button">
						<i class="icon-question-sign"></i>
						{l s='Lost password?'}
					</button>
					<button class="btn btn-default pull-right" name="submitLogin" type="submit">
						<i class="icon-ok text-success"></i>
						{l s='Log in'}
					</button>
				</div>
				<input type="hidden" name="redirect" id="redirect" value="{$redirect}"/>
			</form>

			<form action="#" id="forgot_password_form" method="post" class="hide form-horizontal">
				<h3 class="text-center"><i class="icon-exclamation-sign"></i> {l s='Forgot your password?'}</h3>
				<p class="alert alert-info">{l s='In order to receive your access code by email, please enter the address you provided during the registration process.'}
				</p>	
				<div class="form-group">
					<label for="email_forgot">
						{l s='Email address:'}
					</label>
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-envelope"></i></span>
						<input
							type="text"
							name="email_forgot"
							id="email_forgot"
							class="input email_field form-control"
							autofocus="autofocus"
							placeholder="test@example.com" />
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<button href="#" class="btn btn-default show-login-form">
						<i class="icon-caret-left text-danger"></i>
						{l s='Back to login'}
					</button>
					<button class="btn btn-default pull-right" name="submitLogin" type="submit">
						<i class="icon-ok text-success"></i>
						{l s='Send'}
					</button>
				</div>
			</form>
			{else}
			<div class="col-lg-12">
				<p>{l s='For security reasons, you cannot connect to the Back Office until after you have:'}</p>
				<ul>
					{if isset($wrong_install_name) && $wrong_install_name == true}
						<li>{l s='deleted the /install folder'}</li>
					{/if}
					{if isset($wrong_folder_name) && $wrong_folder_name == true}
						<li>{l s='renamed the /admin folder (e.g. %s)' sprintf=$randomNb}</li>
					{/if}
				</ul>
				<p>
					<a href="{$adminUrl|escape:'htmlall':'UTF-8'}">
						{l s='Please then access this page by the new URL (e.g. %s)' sprintf=$adminUrl}
					</a>
				</p>
			</div>
			{/if}
		</div>
		<div class="col-md-4 col-md-offset-4">
			<p class="text-center text-muted">
				<a href="http://www.prestashop.com">
					&copy; 2005 - {$smarty.now|date_format:"%Y"} Copyright by PrestaShop. all rights reserved.
				</a>
			</p>
			<p class="text-center">
				<a class="link-social link-twitter" href="#" title="Twitter">
					<i class="icon-twitter"></i>
				</a>
				<a class="link-social link-facebook" href="#" title="Facebook">
					<i class="icon-facebook"></i>
				</a>
				<a class="link-social link-github" href="#" title="Github">
					<i class="icon-github"></i>
				</a>
				<a class="link-social link-google" href="#" title="Google">
					<i class="icon-google-plus"></i>
				</a>
			</p>
		</div>
	</div>
</div>