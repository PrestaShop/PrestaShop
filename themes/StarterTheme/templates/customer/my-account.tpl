{extends "page.tpl"}

{block name="page_title"}
  {l s='My account'}
{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-my-account">

    <p>{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</p>

    <ul class="link-list">
      {if $customer.addresses|count}
        <li>
          <a href="{$urls.pages.addresses}">{l s='Addresses'}</a>
        </li>
      {else}
        <li>
          <a href="{$urls.pages.address}">{l s='Add first address'}</a>
        </li>
      {/if}

      <li>
        <a href="{$urls.pages.identity}">{l s='Information'}</a>
      </li>

      <li>
        <a href="{$urls.pages.history}">{l s='Order history and details'}</a>
      </li>

      <li>
        <a href="{$urls.pages.order_slip}">{l s='Credit slips'}</a>
      </li>

      {if $feature_active.voucher}
        <li>
          <a href="{$urls.pages.discount}">{l s='Vouchers'}</a>
        </li>
      {/if}

      {if $feature_active.return}
        <li>
          <a href="{$urls.pages.order_follow}">{l s='Merchandise returns'}</a>
        </li>
      {/if}

      {block name="displayCustomerAccount"}
        {hook h="displayCustomerAccount"}
      {/block}

    </ul>

  </section>
{/block}
