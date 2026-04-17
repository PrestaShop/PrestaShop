{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='customer/page.tpl'}

{$componentName = 'customer-link'}

{block name='page_title'}
  {l s='Welcome' d='Shop.Theme.Customeraccount'} {$customer.firstname} {$customer.lastname}
{/block}

{block name='page_content'}
{block name='display_customer_account_top'}
    {hook h='displayCustomerAccountTop'}
{/block}
  <div class="{$componentName} row g-3">
    <a class="{$componentName}__link col-md-6 col-lg-4" id="identity-link" href="{$urls.pages.identity}">
      <span class="link-item">
        <i class="material-icons" aria-hidden="true">&#xE853;</i>
        {l s='Information' d='Shop.Theme.Customeraccount'}
      </span>
    </a>

    {if $customer.addresses|count}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="addresses-link" href="{$urls.pages.addresses}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE56A;</i>
          {l s='Addresses' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {else}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="address-link" href="{$urls.pages.address}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE567;</i>
          {l s='Add first address' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {/if}

    {if !$configuration.is_catalog}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="history-link" href="{$urls.pages.history}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE916;</i>
          {l s='Order history and details' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {/if}

    {if !$configuration.is_catalog}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="order-slips-link" href="{$urls.pages.order_slip}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE8B0;</i>
          {l s='Credit slips' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {/if}

    {if $configuration.voucher_enabled && !$configuration.is_catalog}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="discounts-link" href="{$urls.pages.discount}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE54E;</i>
          {l s='Vouchers' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {/if}

    {if $configuration.return_enabled && !$configuration.is_catalog}
      <a class="{$componentName}__link col-md-6 col-lg-4" id="returns-link" href="{$urls.pages.order_follow}">
        <span class="link-item">
          <i class="material-icons" aria-hidden="true">&#xE860;</i>
          {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
    {/if}

    {block name='display_customer_account'}
      {hook h='displayCustomerAccount'}
    {/block}
  </div>

  <a class="{$componentName}__logout d-md-none" href="{$urls.actions.logout}">
    <i class="material-icons me-2" aria-hidden="true">exit_to_app</i>
    {l s='Sign out' d='Shop.Theme.Actions'}
  </a>
{/block}

{block name='account_link'}{/block}
