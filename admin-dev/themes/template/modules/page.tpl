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

	<div id="container">
		<!--start sidebar module-->
		<div class="sidebar">
			<div class="categorieTitle">
				<h3>{l s='Categories'}</h3>
				<div class="subHeadline">{$nb_modules}</div>
				<ul class="categorieList">
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
			{include file='modules/list.tpl'}
			{if count($modules)}
				<div style="margin-top: 12px;">
					<input type="button" class="button big" value="Install the selection" onclick="modules_management('install')"/>
					<input type="button" class="button big" value="Uninstall the selection" onclick="modules_management('uninstall')" />
				</div>
			{/if}
		</div>
	</div>

</div>
