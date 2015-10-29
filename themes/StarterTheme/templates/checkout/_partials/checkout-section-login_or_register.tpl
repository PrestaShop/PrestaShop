<div id="checkout-login-or-register">

  <div id="checkout-login">
    {block name="opc_login_form"}
      {include file="customer/_partials/login-form.tpl" back=$urls.pages.order}
    {/block}
  </div>

  <div id="checkout-register">
    <h1 class="h3">{l s='Continue'}</h1>
    {block name="address_form"}
      {include file="customer/_partials/address-form.tpl" address_fields=$address_fields address=$address countries=$countries form_action=$urls.pages.order}
    {/block}
  </div>

</div>
