{extends file='page.tpl'}

{block name='page_title'}
  {l s='My account'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-my-account">

    <h6>{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</h6>

    <div class="links">
      {if $customer.addresses|count}
        <div class="col-lg-4 col-md-6 col-sm-6">
          <a id="addresses-link" href="{$urls.pages.addresses}" class="btn btn-secondary">{l s='Addresses'}</a>
        </div>
      {else}
        <div class="col-lg-4 col-md-6 col-sm-6">
          <a id="address-link" href="{$urls.pages.address}" class="btn btn-secondary">{l s='Add first address'}</a>
        </div>
      {/if}

      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="identity-link" href="{$urls.pages.identity}" class="btn btn-secondary">{l s='Information'}</a>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="history-link" href="{$urls.pages.history}" class="btn btn-secondary">{l s='Order history and details'}</a>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-6">
        <a id="order-slips-link" href="{$urls.pages.order_slip}" class="btn btn-secondary">{l s='Credit slips'}</a>
      </div>

      {if $feature_active.voucher}
        <div class="col-lg-4 col-md-6 col-sm-6">
          <a id="discounts-link" href="{$urls.pages.discount}" class="btn btn-secondary">{l s='Vouchers'}</a>
        </div>
      {/if}

      {if $feature_active.return}
        <div class="col-lg-4 col-md-6 col-sm-6">
          <a id="returns-link" href="{$urls.pages.order_follow}" class="btn btn-secondary">{l s='Merchandise returns'}</a>
        </div>
      {/if}

      {block name='display_customer_account'}
        {hook h='displayCustomerAccount'}
      {/block}
    </div>

  </section>
{/block}
