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

{if $add_permission eq '1'}
<div class="panel">
	{if isset($logged_on_addons)}
			<!--start addons login-->
			<div class="panel-heading">
				<i class="icon-user"></i>
				{l s='You are logged into %s' sprintf='<a target="_blank" href="http://addons.prestashop.com/">PrestaShop Addons</a>'}
			</div>
			<div id="addons_login_div">	
				<p>{l s='Welcome'} {$username_addons}</p>
				<a class="btn btn-default" href="#" id="addons_logout_button">
					<i class="icon-signout"></i> {l s='Log out from PrestaShop Addons.'}
				</a>
			</div>
			<!--end addons login-->
	{else}
		{if $check_url_fopen eq 'ko'  OR $check_openssl eq 'ko'}
			<div class="alert alert-block">
				{l s='If you want to be able to fully use the AdminModules panel and have free modules available, you should enable the following configuration on your server:'}
				<br />
				{if $check_url_fopen eq 'ko'}- {l s='Enable allow_url_fopen'}<br />{/if}
				{if $check_openssl eq 'ko'}- {l s='Enable php openSSL extension'}<br />{/if}
			</div>
		{else}
			<!--start addons login-->
			<div class="panel-heading">
				<i class="icon-user"></i>
				{l s='Do you have a %s account?' sprintf='<a target="_blank" href="http://addons.prestashop.com/">PrestaShop Addons</a>'}
			</div>
			<form id="addons_login_form" method="post" class="form-horizontal">
				<div class="row">
					<label class="control-label col-lg-3">{l s='Addons Login'} :</label> 
					<div class="input-group col-lg-4">
						<span class="input-group-addon"><i class="icon-user"></i></span>
						<input type="text" value="" id="username_addons" autocomplete="off" class="ac_input">
					</div>
				</div>
				<div class="row">
					<label class="control-label col-lg-3">{l s= 'Password Addons'} :</label>
					<div class="input-group col-lg-4">
						<span class="input-group-addon"><i class="icon-key"></i></span>
						<input type="password" value="" id="password_addons" autocomplete="off" class="ac_input">
					</div>
				</div>
				<div class="row">
					<div class="col-lg-10 col-offset-3">
						<button class="btn btn-default" id="addons_login_button" type="submit">
							<i class="icon-unlock"></i> {l s='Log in'}
						</button>
						<div id="addons_loading" class="help-block"></div>
					</div>
				</div>
			</form>
			<!--end addons login-->
		{/if}
	{/if}
	</div>
{/if}