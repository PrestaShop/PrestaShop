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


	{include file='controllers/modules/header.tpl'}

	{if $upgrade_available|@count}
	<div class="alert alert-info">
		{l s='An upgrade is available for some of your modules!'}
		<ul>
		{foreach from=$upgrade_available item='module'}
			<li> &raquo; <a href="{$currentIndex|escape:htmlall}&token={$token|escape:htmlall}&anchor=anchor{$module.anchor|escape:htmlall}"><b>{$module.name|escape:htmlall}</b></a></li>
		{/foreach}
		</ul>
	</div>
	{/if}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-list-ul"></i>
				{l s='Modules list'}
			</div>
			<!--start sidebar module-->
			<div class="row">
				<div class="categorieTitle col-lg-3">
					<div class="list-group">
						<form id="filternameForm" method="post" class="list-group-item form-horizontal">
							<div class="input-group col-lg-12">
								<input type="text" value="" name="filtername" autocomplete="off" class="ac_input">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<i class="icon-search"></i> {l s='Search'}
									</button>
								</span>
							</div>
						</form>
						<a class="list-group-item {if isset($categoryFiltered.favorites)}active{/if}" href="{$currentIndex}&token={$token}&filterCategory=favorites">
							{l s='Favorites'} <span class="badge pull-right">{$nb_modules_favorites}</span>
						</a>
						<a class="list-group-item {if count($categoryFiltered) lte 0}active{/if}" href="{$currentIndex}&token={$token}&unfilterCategory=yes">
							{l s='All'} <span class="badge pull-right">{$nb_modules}</span>
						</a>
						{foreach from=$list_modules_categories item=module_category key=module_category_key}
							<a class="list-group-item {if isset($categoryFiltered[$module_category_key])}active{/if}" href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}">
								{$module_category.name} <span class="badge pull-right">{$module_category.nb}</span>
							</a>
						{/foreach}
					</div>
					{include file='controllers/modules/login_addons.tpl'}
				</div>
				<div id="moduleContainer" class="col-lg-9">
					{include file='controllers/modules/list.tpl'}
				</div>
			</div>
		</div>
	</div>