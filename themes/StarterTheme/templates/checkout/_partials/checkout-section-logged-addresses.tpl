<section id="checkout-addresses">

  <header>
    <h1 class="h3">{l s='Addresses'}</h1>
  </header>

  <ul class="actions">
    <li>
      <a  href="{url entity="address" params=['back' => $urls.pages.order]}"
          data-link-action="add-new-address">
        {l s='Create new address'}
      </a>
    </li>
  </ul>

  <form action="{$urls.pages.cart}" method="POST">
    <div class="addresses-container">

      <div id="select-delivery-address" class="address-selector">
        <h2 class="h3">{l s='Your delivery address'}</h2>
        {block name="opc_delivery_address"}
          {include file="checkout/_partials/address-selector-block.tpl"
          name="id_address_delivery"
          addresses=$customer.addresses
          selected=$cart.id_address_delivery}
        {/block}
      </div>

      {block name="checkout_guest_same_address"}
        <label>
          <input type="checkbox" id="checkout-different-address-for-invoice" data-role="checkout-different-address-for-invoice">
          {l s='Use a different address for invoice'}
        </label>
      {/block}

      <div id="select-invoice-address" class="address-selector ps-shown-by-js">
        <h2 class="h3">{l s='Your invoice address'}</h2>
        {block name="opc_invoice_address"}
          {include file="checkout/_partials/address-selector-block.tpl"
          name="id_address_invoice"
          addresses=$customer.addresses
          selected=$cart.id_address_invoice}
        {/block}
      </div>

    </div>

    <input type="hidden" name="token" value="{$static_token}">
    <input type="hidden" name="back" value="{$urls.pages.order|urlencode}">
    <input type="hidden" name="changeAddresses" value="1">

    <button type="submit">
      {l s='Save'}
    </button>
  </form>

</section>
