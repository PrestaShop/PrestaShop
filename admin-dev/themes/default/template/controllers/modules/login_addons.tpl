{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="modal-body">
{if $add_permission eq '1'}
	<div class="panel">
	{if !isset($logged_on_addons) || !$logged_on_addons}
		{if $check_url_fopen eq 'ko'  OR $check_openssl eq 'ko'}
			<div class="alert alert-warning">
				{l s='If you want to be able to fully use the AdminModules panel and have free modules available, you should enable the following configuration on your server:'}
				<br />
				{if $check_url_fopen eq 'ko'}- {l s='Enable PHP\'s allow_url_fopen setting'}<br />{/if}
				{if $check_openssl eq 'ko'}- {l s='Enable PHP\'s OpenSSL extension'}<br />{/if}
			</div>
		{else}
			<!--start addons login-->
			<form id="addons_login_form" method="post" >
				<p>{l s="Connect your shop with PrestaShop's marketplace in order to automatically import all your Addons purchases."}</p>
				<div class="row">
					<div class="col-lg-6">
						<h3>Connect to PrestaShop Addons</h3>
						<hr>
						<div class="form-group">
							<!-- <label for="username_addons">{l s='Addons login'}</label>  -->
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-user"></i></span>
								<input id="username_addons" class="form-control" name="username_addons" type="text" value=""  autocomplete="off" class="form-control ac_input">
							</div>
						</div>

						<div class="form-group">
							<!-- <label for="password_addons">{l s= 'Addons password'}</label> -->
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-key"></i></span>
								<input id="password_addons" class="form-control" name="password_addons" type="password" value=""  autocomplete="off" class="form-control ac_input">
							</div>
						</div>
						<div class="form-group">
							<button id="addons_login_button" class="btn btn-primary btn-block btn-lg" type="submit">
								<i class="icon-unlock"></i> {l s='Sign in'}
							</button>
						</div>

					</div>
					<div class="col-lg-6">
						<h3>{l s="Don't have an account?"}</h3>
						<p class='intro'>{l s="Discover the Power of PrestaShop Addons! Explore the PrestaShop Official Marketplace and find over 3 500 innovative modules and themes that optimize conversion rates, increase traffic, build customer loyalty and maximize your productivity"}</p>
						<a class="btn btn-default btn-block btn-lg" target="_blank" href="{$addons_register_link}">
							{l s="Create an Account"}
							<i class="icon-external-link"></i>
						</a>
					</div>
				</div>					

				<div id="addons_loading" class="help-block"></div>

			</form>
			<!--end addons login-->
		{/if}
	{/if}
	</div>
{/if}
</div>
