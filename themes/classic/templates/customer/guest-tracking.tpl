{extends "customer/order-detail.tpl"}

{block name="page_title"}
  {l s='Guest Tracking'}
{/block}

{block name="order_detail"}
  {include file='customer/_partials/order-detail-no-return.tpl'}
{/block}

{block name="order_messages"}
{/block}

{block name="page_content" append}
  {block name="guest_to_customer"}
    <form action="{$urls.pages.guest_tracking}" method="post">
      <header>
        <h1 class="h3">{l s='Transform your guest account into a customer account and enjoy:'}</h1>
        <ul>
          <li> -{l s='Personalized and secure access'}</li>
          <li> -{l s='Fast and easy checkout'}</li>
          <li> -{l s='Easier merchandise return'}</li>
        </ul>
      </header>

      <section class="form-fields">

        <label>
          <span>{l s='Set your password:'}</span>
          <input type="password" data-validate="isPasswd" name="password" value="" />
        </label>

      </section>

      <footer class="form-footer">
        <input type="hidden" name="submitTransformGuestToCustomer" value="1">
        <input type="hidden" name="id_order" value="{$order.data.id}">
        <input type="hidden" name="order_reference" value="{$order.data.reference}">
        <input type="hidden" name="email" value="{$order.customer.email}">

        <button type="submit">{l s='Send'}</button>
      </footer>

  {/block}
{/block}
