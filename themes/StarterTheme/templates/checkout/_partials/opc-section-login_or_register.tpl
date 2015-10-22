<div id="opc-login-or-register">

  <div id="opc-login">
    {block name="opc_login_form"}
      {include file="customer/_partials/login-form.tpl" back=$urls.pages.order_opc}
    {/block}
  </div>

  <div id="opc-register">
    <h1 class="h3">{l s='Continue'}</h1>
    {block name="address_form"}
      {include file="customer/_partials/address-form.tpl" address_fields=$address_fields address=$address countries=$countries form_action=$urls.pages.order_opc}
    {/block}
  </div>

</div>
