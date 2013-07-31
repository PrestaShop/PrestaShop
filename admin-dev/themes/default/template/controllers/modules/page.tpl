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

<div id="productBox">
	{include file='controllers/modules/header.tpl'}
	{include file='controllers/modules/filters.tpl'}
	{if $upgrade_available|@count}
		<div class="alert alert-info" style="display:block;">
			{l s='An upgrade is available for some of your modules!'}
			<ul>
			{foreach from=$upgrade_available item='module'}
				<li> &raquo; <a href="{$currentIndex|escape:htmlall}&token={$token|escape:htmlall}&anchor=anchor{$module.anchor|escape:htmlall}"><b>{$module.name|escape:htmlall}</b></a></li>
			{/foreach}
			</ul>
		</div>
	{/if}
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group pull-right">
				<a class="btn btn-default" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}">
					<i class="icon-list"></i>
					{l s='Normal view'} 
				</a>
				<a class="btn btn-default" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites">
					<i class="icon-star"></i> 
					{l s='Favorites view'}
				</a>
			</div>
		</div>
		<div id="container">
			<!--start sidebar module-->
			<div class="row-fluid">
				<div class="categorieTitle col-lg-3">
					<div class="list-group">
						<h3>
							<i class="icon-reorder"></i>
							{l s='Categories'}
						</h3>
						<a class="list-group-item {if isset($categoryFiltered.favorites)}active{/if}" href="{$currentIndex}&token={$token}&filterCategory=favorites">
							{l s='Favorites'} <span class="badge pull-right">{$nb_modules_favorites}</span>
						</a>
					
						<a class="list-group-item {if count($categoryFiltered) lte 0}active{/if}" href="{$currentIndex}&token={$token}&unfilterCategory=yes">
							{l s='Total'} <span class="badge pull-right">{$nb_modules}</span>
						</a>
						{foreach from=$list_modules_categories item=module_category key=module_category_key}
							<a class="list-group-item {if isset($categoryFiltered[$module_category_key])}active{/if}" href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}">
								{$module_category.name} <span class="badge pull-right">{$module_category.nb}</span>
							</a>
						{/foreach}
					</div>
				</div>
			</div>
			<div id="moduleContainer" class="col-lg-9">
				<h3>
					<i class="icon-list-ul"></i>
					{l s='Modules list'}
				</h3>
				{include file='controllers/modules/list.tpl'}
			</div>
		</div>
	</div>
</div>