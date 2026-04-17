{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<form id="header_search" class="component bo_search_form" method="post" action="{$link->getAdminLink('AdminSearch')}" role="search">
	<div class="form-group">
		<input type="hidden" name="bo_search_type" id="bo_search_type" />
		<div class="input-group">
			<div class="input-group-btn">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i id="search_type_icon" class="material-icons">search</i>
				</button>
				<ul id="header_search_options" class="dropdown-menu">
					<li class="search-all search-option active">
						<a href="#" data-value="0" data-placeholder="{l s='What are you looking for?' d='Admin.Navigation.Header'}">
							<i class="material-icons">search</i> {l s='Everywhere' d='Admin.Navigation.Header'}</a>
					</li>
					<li class="divider"></li>
					<li class="search-book search-option">
						<a href="#" data-value="1" data-placeholder="{l s='Product name, SKU, reference...' d='Admin.Navigation.Header'}">
							<i class="material-icons">store_mall_directory</i> {l s='Catalog' d='Admin.Navigation.Menu'}
						</a>
					</li>
					<li class="search-customers-name search-option">
						<a href="#" data-value="2" data-placeholder="{l s='Email, name...' d='Admin.Navigation.Header'}">
							<i class="material-icons">group</i> {l s='Customers by name' d='Admin.Navigation.Header'}
						</a>
					</li>
					<li class="search-customers-addresses search-option">
						<a href="#" data-value="6" data-placeholder="{l s='123.45.67.89'}" data-icon="icon-desktop">
							<i class="material-icons">desktop_mac</i> {l s='Customers by IP address' d='Admin.Navigation.Header'}</a>
					</li>
					<li class="search-orders search-option">
						<a href="#" data-value="3" data-placeholder="{l s='Order ID' d='Admin.Navigation.Header'}">
							<i class="material-icons">shopping_basket</i> {l s='Orders' d='Admin.Global'}
						</a>
					</li>
					<li class="search-invoices search-option">
						<a href="#" data-value="4" data-placeholder="{l s='Invoice Number' d='Admin.Navigation.Header'}">
							<i class="material-icons">book</i> {l s='Invoices' d='Admin.Navigation.Menu'}
						</a>
					</li>
					<li class="search-carts search-option">
						<a href="#" data-value="5" data-placeholder="{l s='Cart ID' d='Admin.Navigation.Header'}">
							<i class="material-icons">shopping_cart</i> {l s='Carts' d='Admin.Global'}
						</a>
					</li>
					<li class="search-modules search-option">
						<a href="#" data-value="7" data-placeholder="{l s='Module name' d='Admin.Navigation.Header'}">
							<i class="material-icons">extension</i> {l s='Modules' d='Admin.Global'}
						</a>
					</li>
				</ul>
			</div>
			{if isset($show_clear_btn) && $show_clear_btn}
			<a href="#" class="clear_search hide"><i class="material-icons">clear</i></a>
			{/if}
			<input id="bo_query" name="bo_query" type="text" class="form-control" value="{$bo_query}" placeholder="{l s='Search' d='Admin.Actions'}" aria-label="{l s='Search' d='Admin.Actions'}" />
		</div>
	</div>
	<script>
		{if isset($search_type) && $search_type}
			$(function() {
				$('.search-option a[data-value='+{$search_type|intval}+']').click();
			});
		{/if}
	</script>
</form>
