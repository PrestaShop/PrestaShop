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

<form id="header_search"
      class="bo_search_form dropdown-form js-dropdown-form collapsed"
      method="post"
      action="{$baseAdminUrl}index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}"
      role="search">
  <input type="hidden" name="bo_search_type" id="bo_search_type" class="js-search-type" />
  {if isset($show_clear_btn) && $show_clear_btn}
    <a href="#" class="clear_search hide"><i class="icon-remove"></i></a>
  {/if}
  <div class="input-group">
    <input type="text" class="form-control js-form-search" id="bo_query" name="bo_query" value="{$bo_query}" placeholder="{l s='Search (e.g.: product reference, customer name…)' d='Admin.Navigation.Header'}" aria-label="{l s='Searchbar' d='Admin.Navigation.Header'}">
    <div class="input-group-append">
      <button type="button" class="btn btn-outline-secondary dropdown-toggle js-dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {l s='Everywhere' d='Admin.Navigation.Header'}
      </button>
      <div class="dropdown-menu js-items-list">
        <a class="dropdown-item" data-item="{l s='Everywhere' d='Admin.Navigation.Header'}" href="#" data-value="0" data-placeholder="{l s='What are you looking for?' d='Admin.Navigation.Header'}" data-icon="icon-search"><i class="material-icons">search</i> {l s='Everywhere' d='Admin.Navigation.Header'}</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" data-item="{l s='Catalog' d='Admin.Global'}" href="#" data-value="1" data-placeholder="{l s='Product name, reference, etc.' d='Admin.Navigation.Header'}" data-icon="icon-book"><i class="material-icons">store_mall_directory</i> {l s='Catalog' d='Admin.Global'}</a>
        <a class="dropdown-item" data-item="{l s='Customers' d='Admin.Navigation.Header'} {l s='by name'}" href="#" data-value="2" data-placeholder="{l s='Name' d='Admin.Navigation.Header'}" data-icon="icon-group"><i class="material-icons">group</i> {l s='Customers' d='Admin.Navigation.Header'} {l s='by name' d='Admin.Navigation.Header'}</a>
        <a class="dropdown-item" data-item="{l s='Customers' d='Admin.Navigation.Header'} {l s='by ip address'}" href="#" data-value="6" data-placeholder="{l s='123.45.67.89' d='Admin.Navigation.Header'}" data-icon="icon-desktop"><i class="material-icons">desktop_mac</i> {l s='Customers' d='Admin.Navigation.Header'} {l s='by IP address' d='Admin.Navigation.Header'}</a>
        <a class="dropdown-item" data-item="{l s='Orders' d='Admin.Global'}" href="#" data-value="3" data-placeholder="{l s='Order ID' d='Admin.Navigation.Header'}" data-icon="icon-credit-card"><i class="material-icons">shopping_basket</i> {l s='Orders' d='Admin.Global'}</a>
        <a class="dropdown-item" data-item="{l s='Invoices' d='Admin.Global'}" href="#" data-value="4" data-placeholder="{l s='Invoice number' d='Admin.Navigation.Header'}" data-icon="icon-book"><i class="material-icons">book</i> {l s='Invoices' d='Admin.Global'}</a>
        <a class="dropdown-item" data-item="{l s='Carts' d='Admin.Global'}" href="#" data-value="5" data-placeholder="{l s='Cart ID' d='Admin.Navigation.Header'}" data-icon="icon-shopping-cart"><i class="material-icons">shopping_cart</i> {l s='Carts' d='Admin.Global'}</a>
        <a class="dropdown-item" data-item="{l s='Modules' d='Admin.Global'}" href="#" data-value="7" data-placeholder="{l s='Module name' d='Admin.Navigation.Header'}" data-icon="icon-puzzle-piece"><i class="material-icons">extension</i> {l s='Modules' d='Admin.Global'}</a>
      </div>
      <button class="btn btn-primary" type="submit"><span class="d-none">{l s='SEARCH' d='Admin.Navigation.Header'}</span><i class="material-icons">search</i></button>
    </div>
  </div>
</form>

<script type="text/javascript">
 $(document).ready(function(){
  {if isset($search_type) && $search_type}
    $('.search-option a[data-value='+{$search_type|intval}+']').click();
  {/if}
  $('#bo_query').one('click', function() {
    $(this).closest('form').removeClass('collapsed');
  });
});
</script>
