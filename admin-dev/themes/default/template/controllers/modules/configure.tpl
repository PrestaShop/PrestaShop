{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
{extends file="page_header_toolbar.tpl"}


{block name=pageTitle}
<h2 class="page-title">
	{l s='Configure' d='Admin.Actions'}
</h2>
<h4 class="page-subtitle">{$module_display_name}</h4>
{/block}
{block name=pageBreadcrumb}
<ul class="breadcrumb page-breadcrumb">
	{if $breadcrumbs2.container.name != ''}
		<li class="breadcrumb-current">
			{$breadcrumbs2.container.name|escape}
		</li>
	{/if}
	<li>{$module_name}</li>
	<li>
		<i class="icon-wrench"></i>
		{l s='Configure' d='Admin.Actions'}
	</li>
</ul>
{/block}
{block name=toolbarBox}
<div class="page-bar toolbarBox">
	<div class="btn-toolbar">
		<ul class="nav nav-pills pull-right">
			<li>
				<a id="desc-module-back" class="toolbar_btn" href="{url entity='sf' route='admin_module_manage'}">
					<i class="process-icon-back"></i>
					<div>{l s='Back' d='Admin.Global'}</div>
				</a>
			</li>
			{if !empty($translateLinks)}
			<li>
				<a id="desc-module-translate" data-toggle="modal" data-target="#moduleTradLangSelect" class="toolbar_btn" href="#" title="{l s='Translate'}">
					<i class="process-icon-flag"></i>
					<div>{l s='Translate' d='Admin.Actions'}</div>
				</a>
			</li>
			{/if}
			{if isset($module_rtl_link)}
			<li>
				<a id="desc-module-rtl" class="toolbar_btn" href="{$module_rtl_link}" title="{l s='Generate RTL Stylesheets'}">
					<i class="process-icon-rtl"></i>
					<div>{l s='RTL Module' d='Admin.Modules.Feature'}</div>
				</a>
			</li>
			{/if}
			<li>
				<a id="desc-module-hook" class="toolbar_btn" href="{$module_hook_link}" title="{l s='Manage hooks'}">
					<i class="process-icon-anchor"></i>
					<div>{l s='Manage hooks' d='Admin.Modules.Feature'}</div>
				</a>
			</li>
			{hook h="displayModuleConfigureExtraButtons" module_name=$module_name}
		</ul>
	</div>
</div>


{/block}
