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
*  @version  Release: $Revision: 9771 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="productBox">

	{include file='modules/header.tpl'}
	{include file='modules/filters.tpl'}


	{if !isset($logged_on_addons)}
		<!--start addons login-->
		<div class="filter-module" id="addons_login_div">
			<form id="addons_login_form" method="post">
				<input type="text" value="" id="username_addons" autocomplete="off" class="ac_input">
				<input type="text" value="" id="password_addons" autocomplete="off" class="ac_input">
				<input type="submit" class="button" id="addons_login_button" value="{l s='Log in'}">
				<span id="addons_loading" style="color:red"></span>
			</form>

		</div>
		<!--end addons login-->
	{/if}




	<div id="container">
		<!--start sidebar module-->
		<div class="sidebar">
			<div class="categorieTitle">
				<h3>{l s='Categories'}</h3>
				<div class="subHeadline">{$nb_modules}</div>
				<ul class="categorieList">
					{foreach from=$list_modules_categories item=module_category key=module_category_key}
						<li {if isset($categoryFiltered[$module_category_key])}style="background-color:#EBEDF4"{/if}>
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}"><span>{$module_category.name}</span></a></div>
							<div class="count">{$module_category.nb}</div>
						</li>
					{/foreach}
				</ul>
			</div>
		</div>

		<div id="moduleContainer">
			{include file='modules/list.tpl'}
		</div>
	</div>

</div>
