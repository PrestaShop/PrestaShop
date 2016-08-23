{extends file='page.tpl'}

{block name='page_title'}
  {l s='Your account' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <div class="links">

    <div class="col-lg-4 col-md-6 col-sm-6">
      <a id="identity-link" href="{$urls.pages.identity}">
        <i class="material-icons">&#xE853;</i>
        {l s='Information' d='Shop.Theme.CustomerAccount'}
      </a>
    </div>

    {if $customer.addresses|count}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="addresses-link" href="{$urls.pages.addresses}">
          <i class="material-icons">&#xE56A;</i>
          {l s='Addresses' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {else}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="address-link" href="{$urls.pages.address}">
          <i class="material-icons">&#xE567;</i>
          {l s='Add first address' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/if}

    {if !$configuration.is_catalog}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="history-link" href="{$urls.pages.history}">
          <i class="material-icons">&#xE916;</i>
          {l s='Order history and details' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/if}

    {if !$configuration.is_catalog}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="order-slips-link" href="{$urls.pages.order_slip}">
          <i class="material-icons">&#xE8B0;</i>
          {l s='Credit slips' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/if}

    {if $configuration.voucher_enabled && !$configuration.is_catalog}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="discounts-link" href="{$urls.pages.discount}">
          <i class="material-icons">&#xE54E;</i>
          {l s='Vouchers' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/if}

    {if $configuration.return_enabled && !$configuration.is_catalog}
      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="returns-link" href="{$urls.pages.order_follow}">
          <i class="material-icons">&#xE860;</i>
          {l s='Merchandise returns' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/if}

    {block name='display_customer_account'}
      {hook h='displayCustomerAccount'}
    {/block}

    <div class="clearfix"></div>
  </div>
{/block}


{block name='page_footer'}
  {block name='my_account_links'}
    <div class="text-xs-center">
      <a href="{$logout_url}" >
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
    </div>
  {/block}
{/block}
