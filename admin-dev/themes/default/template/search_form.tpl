{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<form id="header_search" class="component bo_search_form" method="post" action="{$baseAdminUrl}index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}" role="search">
	<div class="form-group">
		<input type="hidden" name="bo_search_type" id="bo_search_type" />
		<div class="input-group">
			<div class="input-group-btn">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i id="search_type_icon" class="material-icons">search</i>
				</button>
				<ul id="header_search_options" class="dropdown-menu">
					<li class="search-all search-option active">
						<a href="#" data-value="0" data-placeholder="{l s='What are you looking for?' d='Admin.Navigation.Header'}" data-icon="icon-search">
							<i class="icon-search"></i> {l s='Everywhere' d='Admin.Navigation.Header'}</a>
					</li>
					<li class="divider"></li>
					<li class="search-book search-option">
						<a href="#" data-value="1" data-placeholder="{l s='Product name, SKU, reference...' d='Admin.Navigation.Header'}" data-icon="icon-book">
							<i class="icon-book"></i> {l s='Catalog' d='Admin.Navigation.Menu'}
						</a>
					</li>
					<li class="search-customers-name search-option">
						<a href="#" data-value="2" data-placeholder="{l s='Email, name...' d='Admin.Navigation.Header'}" data-icon="icon-group">
							<i class="icon-group"></i> {l s='Customers by name' d='Admin.Navigation.Header'}
						</a>
					</li>
					<li class="search-customers-addresses search-option">
						<a href="#" data-value="6" data-placeholder="{l s='123.45.67.89'}" data-icon="icon-desktop">
							<i class="icon-desktop"></i> {l s='Customers by IP address' d='Admin.Navigation.Header'}</a>
					</li>
					<li class="search-orders search-option">
						<a href="#" data-value="3" data-placeholder="{l s='Order ID' d='Admin.Navigation.Header'}" data-icon="icon-credit-card">
							<i class="icon-credit-card"></i> {l s='Orders' d='Admin.Global'}
						</a>
					</li>
					<li class="search-invoices search-option">
						<a href="#" data-value="4" data-placeholder="{l s='Invoice Number' d='Admin.Navigation.Header'}" data-icon="icon-book">
							<i class="icon-book"></i> {l s='Invoices' d='Admin.Navigation.Menu'}
						</a>
					</li>
					<li class="search-carts search-option">
						<a href="#" data-value="5" data-placeholder="{l s='Cart ID' d='Admin.Navigation.Header'}" data-icon="icon-shopping-cart">
							<i class="icon-shopping-cart"></i> {l s='Carts' d='Admin.Global'}
						</a>
					</li>
					<li class="search-modules search-option">
						<a href="#" data-value="7" data-placeholder="{l s='Module name' d='Admin.Navigation.Header'}" data-icon="icon-puzzle-piece">
							<i class="icon-puzzle-piece"></i> {l s='Modules' d='Admin.Global'}
						</a>
					</li>
				</ul>
			</div>
			{if isset($show_clear_btn) && $show_clear_btn}
			<a href="#" class="clear_search hide"><i class="icon-remove"></i></a>
			{/if}
			<input id="bo_query" name="bo_query" type="text" class="form-control" value="{$bo_query}" placeholder="{l s='Search' d='Admin.Actions'}" />
<!--  							<span class="input-group-btn">
				<button type="submit" id="bo_search_submit" class="btn btn-primary">
					<i class="icon-search"></i>
				</button>
			</span> -->
		</div>
	</div>

	<script>
		{if isset($search_type) && $search_type}
			$(document).ready(function() {
				$('.search-option a[data-value='+{$search_type|intval}+']').click();
			});
		{/if}
	</script>
</form>
