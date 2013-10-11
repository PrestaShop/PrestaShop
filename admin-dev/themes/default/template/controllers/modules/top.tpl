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
{$kpis}

<div class="page-head">
	<h2 class="page-title">
		{l s='List of modules'}
	</h2>
	<div class="page-bar toolbarBox">
		<div class="btn-toolbar">
			<ul class="cc_button nav nav-pills pull-right">
				{if $add_permission eq '1'}
				<li>
					<a id="desc-module-new" class="toolbar_btn" href="#top_container" onclick="$('#module_install').slideToggle();" title="{l s='Add a new module'}">
						<i class="process-icon-new-module" ></i>
						<div>{l s='Add a new module'}</div>
					</a>
				</li>
				{/if}
			</ul>
		</div>
	</div>
</div>