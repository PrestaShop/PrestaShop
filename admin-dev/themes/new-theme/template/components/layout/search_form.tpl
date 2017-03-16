{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}


<form id="header_search"
      class="bo_search_form dropdown-form js-dropdown-form"
      method="post"
      action="{$baseAdminUrl}index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}"
      role="search">
  <input type="hidden" name="bo_search_type" id="bo_search_type" class="js-search-type" />
  {if isset($show_clear_btn) && $show_clear_btn}
    <a href="#" class="clear_search hide"><i class="icon-remove"></i></a>
  {/if}
  <div class="input-group">
    <input id="bo_query" name="bo_query" type="search" class="form-control dropdown-form-search js-form-search" value="{$bo_query}" placeholder="{l s='Search (e.g.: product reference, customer name…)'}" />
    <div class="input-group-addon">
      <div class="dropdown">
        <span class="dropdown-toggle js-dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          {l s='Everywhere'}
        </span>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu">
          <ul class="items-list js-items-list">
            <li class="search-all search-option active">
              <a class="dropdown-item" data-item="{l s='Everywhere'}" href="#" data-value="0" data-placeholder="{l s='What are you looking for?'}" data-icon="icon-search">
              <i class="material-icons">search</i> {l s='Everywhere'}</a>
            </li>
            <hr>
            <li class="search-book search-option">
              <a class="dropdown-item" data-item="{l s='Catalog'}" href="#" data-value="1" data-placeholder="{l s='Product name, SKU, reference...'}" data-icon="icon-book">
                <i class="material-icons">library_books</i> {l s='Catalog'}
              </a>
            </li>
            <li class="search-customers-name search-option">
              <a class="dropdown-item" data-item="{l s='Customers'} {l s='by name'}" href="#" data-value="2" data-placeholder="{l s='Email, name...'}" data-icon="icon-group">
                <i class="material-icons">group</i> {l s='Customers'} {l s='by name'}
              </a>
            </li>
            <li class="search-customers-addresses search-option">
              <a class="dropdown-item" data-item="{l s='Customers'} {l s='by ip address'}" href="#" data-value="6" data-placeholder="{l s='123.45.67.89'}" data-icon="icon-desktop">
                <i class="material-icons">desktop_windows</i>{l s='Customers'} {l s='by IP address'}</a>
            </li>
            <li class="search-orders search-option">
              <a class="dropdown-item" data-item="{l s='Orders'}" href="#" data-value="3" data-placeholder="{l s='Order ID'}" data-icon="icon-credit-card">
                <i class="material-icons">credit_card</i> {l s='Orders'}
              </a>
            </li>
            <li class="search-invoices search-option">
              <a class="dropdown-item" data-item="{l s='Invoices'}" href="#" data-value="4" data-placeholder="{l s='Invoice Number'}" data-icon="icon-book">
                <i class="material-icons">book</i></i> {l s='Invoices'}
              </a>
            </li>
            <li class="search-carts search-option">
              <a class="dropdown-item" data-item="{l s='Carts'}" href="#" data-value="5" data-placeholder="{l s='Cart ID'}" data-icon="icon-shopping-cart">
                <i class="material-icons">shopping_cart</i> {l s='Carts'}
              </a>
            </li>
            <li class="search-modules search-option">
              <a class="dropdown-item" data-item="{l s='Modules'}" href="#" data-value="7" data-placeholder="{l s='Module name'}" data-icon="icon-puzzle-piece">
                <i class="material-icons">view_module</i> {l s='Modules'}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="input-group-addon search-bar">
      <button type="submit">{l s='SEARCH'}<i class="material-icons">search</i></button>
    </div>
  </div>
</form>

<script type="text/javascript">
 $(document).ready(function(){
  {if isset($search_type) && $search_type}
    $('.search-option a[data-value='+{$search_type|intval}+']').click();
  {/if}
});
</script>
