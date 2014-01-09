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
<div class="bootstrap">
	<div class="page-head">
		<h2 class="page-title">
			{l s='Configure module %s' sprintf=$module_display_name}
		</h2>
		<ul class="breadcrumb page-breadcrumb">
			{if $breadcrumbs2.container.name != ''}
				<li>
					{if $breadcrumbs2.container.href != ''}<a href="{$breadcrumbs2.container.href|escape}">{/if}
					{if $breadcrumbs2.container.icon != ''}<i class="{$breadcrumbs2.container.icon|escape}"></i>{/if}
					{$breadcrumbs2.container.name|escape}
					{if $breadcrumbs2.container.href != ''}</a>{/if}
				</li>
			{/if}
			<li>{$module_name}</li>
			<li>
				<i class="icon-wrench"></i>
				{l s='Configure'}
			</li>
		</ul>
		<div class="page-bar toolbarBox">
			<div class="btn-toolbar">
				<ul class="nav nav-pills pull-right">
					<li>
						<a id="desc-module-hook" class="toolbar_btn" href="{$module_disable_link}" title="{l s='Disable'}">
							<i class="process-icon-off"></i>
							<div>{l s='Disable'}</div>
						</a>
					</li>
					<li>
						<a id="desc-module-hook" class="toolbar_btn" href="{$module_uninstall_link}" title="{l s='Uninstall'}">
							<i class="process-icon-uninstall"></i>
							<div>{l s='Uninstall'}</div>
						</a>
					</li>
					<li>
						<a id="desc-module-hook" class="toolbar_btn" href="{$module_reset_link}" title="{l s='Reset'}">
							<i class="process-icon-reset"></i>
							<div>{l s='Reset'}</div>
						</a>
					</li>
					{if isset($module_update_link)}
					<li>
						<a id="desc-module-hook" class="toolbar_btn" href="{$module_update_link}" title="{l s='Update'}">
							<i class="process-icon-refresh"></i>
							<div>{l s='Check and update'}</div>
						</a>
					</li>
					{/if}
					<li>
						<a id="desc-module-hook" class="toolbar_btn" href="{$module_hook_link}" title="{l s='Manage hooks'}">
							<i class="process-icon-anchor"></i>
							<div>{l s='Manage hooks'}</div>
						</a>
					</li>
					<li>
						<a id="desc-module-back" class="toolbar_btn" href="{$back_link}" title="{l s='Back'}">
							<i class="process-icon-back" ></i>
							<div>{l s='Back'}</div>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>