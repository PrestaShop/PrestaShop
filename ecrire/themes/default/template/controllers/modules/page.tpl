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
*  @version  Release: $Revision: 9771 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="productBox">

	{include file='controllers/modules/header.tpl'}
	{include file='controllers/modules/filters.tpl'}

	<ul class="view-modules">
		<li class="button normal-view-disabled"><img src="themes/default/img/modules_view_layout_sidebar.png" alt="{l s='Normal view'}" border="0" /><span>{l s='Normal view'}</span></li>
		<li class="button favorites-view"><a  href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites"><img src="themes/default/img/modules_view_table_select_row.png" alt="{l s='Favorites view'}" border="0" /><span>{l s='Favorites view'}</span></a></li>
	
	</ul>

	<div id="container">
		<!--start sidebar module-->
		<div class="sidebar">
			<div class="categorieTitle">
				<h3>{l s='Categories'}</h3>
				<div class="subHeadline">&nbsp;</div>
				<ul class="categorieList">
					<li {if isset($categoryFiltered.favorites)}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&filterCategory=favorites"><span><b>{l s='Favorites'}</b></span></a></div>
							<div class="count"><b>{$nb_modules_favorites}</b></div>
					</li>
					<li {if count($categoryFiltered) lte 0}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&unfilterCategory=yes"><span><b>{l s='Total'}</b></span></a></div>
							<div class="count"><b>{$nb_modules}</b></div>
					</li>
					{foreach from=$list_modules_categories item=module_category key=module_category_key}
						<li {if isset($categoryFiltered[$module_category_key])}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}"><span>{$module_category.name}</span></a></div>
							<div class="count">{$module_category.nb}</div>
						</li>
					{/foreach}
				</ul>
			</div>
		</div>

		<div id="moduleContainer">
			{include file='controllers/modules/list.tpl'}
		</div>
	</div>

</div>
