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
	<div id="login-panel">
		<div id="login-header" class="animated fadeIn">
			<h1 class="text-center">
				<img id="logo" width="40px" src="{$img_dir}/icon-prestashop.svg"/>
				PRESTASHOP
			</h1>
			<hr/>
			<h4 class="text-center">{$shop_name}</h4>
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
		</div>
		<div class="flip-container animated fadeInDown">
			<div class="flipper">
				<div class="front panel">
					{if !isset($wrong_folder_name) && !isset($wrong_install_name)}
					<form action="#" id="login_form" method="post">
						<div class="form-group">
							<label class="control-label" for="email">{l s='Email address'}</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-envelope"></i></span>
								<input
									name="email"
									type="text"
									id="email"
									class="form-control"
									value="{if isset($email)}{$email|escape:'htmlall':'UTF-8'}{/if}"
									autofocus="autofocus"
									tabindex="1"
									placeholder="test@example.com" />
							</div>
						</div>
						<div class="form-group">
								<a href="#" class="show-forgot-password pull-right" >
									{l s='Lost password'}
								</a>
							<label class="control-label" for="passwd">
								{l s='Password'}
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-key"></i></span>
								<input
									name="passwd"
									type="password"
									id="passwd"
									class="form-control"
									value="{if isset($password)}{$password}{/if}"
									tabindex="2"
									placeholder="{l s='Password'}" />
							</div>
						</div>
						<div class="form-group">
							<div class="checkbox">
								<label for="stay_logged_in">
									<input name="stay_logged_in" type="checkbox" id="stay_logged_in" value="1"	tabindex="3"/>
									{l s='Keep me logged in'}
								</label>
							</div>
						</div>
						<hr/>
						<div class="panel-footer">
							<button name="submitLogin" type="submit" tabindex="4" class="btn btn-default btn-lg btn-block ladda-button" data-style="slide-up" data-spinner-color="black" >
								<span class="ladda-label">
									<i class="icon-ok text-success"></i>
									{l s='Log in'}
								</span>
							</button>
						</div>
						<input type="hidden" name="redirect" id="redirect" value="{$redirect}"/>
					</form>
				</div>

				<div class="back panel">
					<form action="#" id="forgot_password_form" method="post">
						<div class="alert alert-info">
							<h4 class="text-center">
								<i class="icon-exclamation-sign"></i>
								{l s='Forgot your password?'}
							</h4>
							<p>{l s='In order to receive your access code by email, please enter the address you provided during the registration process.'}</p>
						</div>
						<div class="form-group">
							<label class="control-label" for="email_forgot">
								{l s='Email'}
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-envelope"></i></span>
								<input
									type="text"
									name="email_forgot"
									id="email_forgot"
									class="form-control"
									autofocus="autofocus"
									tabindex="5"
									placeholder="test@example.com" />
							</div>
						</div>
						<div class="panel-footer">
							<button href="#" class="btn btn-default show-login-form" tabindex="7">
								<i class="icon-caret-left"></i>
								{l s='Back to login'}
							</button>
							<button class="btn btn-default pull-right" name="submitLogin" type="submit" tabindex="6">
								<i class="icon-ok text-success"></i>
								{l s='Send'}
							</button>
						</div>
					</form>
				</div>
			</div>
			{else}
			<div class="alert alert-danger">
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
	</div>
	<div id="login-footer" class="animated fadeIn">
		<p class="text-center text-muted">
			<a href="http://www.prestashop.com/" onclick="return !window.open(this.href);">
				&copy; PrestaShop&#8482; 2005-{$smarty.now|date_format:"%Y"} - All rights reserved
			</a>
		</p>
		<p class="text-center">
			<a class="link-social link-twitter" href="https://twitter.com/PrestaShop" target="_blank" title="Twitter">
				<i class="icon-twitter"></i>
			</a>
			<a class="link-social link-facebook" href="https://www.facebook.com/prestashop" target="_blank" title="Facebook">
				<i class="icon-facebook"></i>
			</a>
			<a class="link-social link-github" href="https://github.com/PrestaShop/PrestaShop/" target="_blank" title="Github">
				<i class="icon-github"></i>
			</a>
			<a class="link-social link-google" href="https://plus.google.com/+prestashop/" target="_blank" title="Google">
				<i class="icon-google-plus"></i>
			</a>
		</p>
	</div>