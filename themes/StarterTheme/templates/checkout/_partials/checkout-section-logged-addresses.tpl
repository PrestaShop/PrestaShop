<section id="checkout-addresses">

  <ul class="actions">
    <li>
      <a  href="{url entity="address" params=['back' => $urls.pages.order]}"
          data-link-action="add-new-address">
        {l s='Create new address'}
      </a>
    </li>
  </ul>

  <form action="{$urls.pages.order}" method="POST">
    <div class="addresses-container">

      <div id="checkout-address-delivery" class="address-selector">
        <h2 class="h3">{l s='Your delivery address'}</h2>
        {block name="opc_delivery_address"}
          {include file="checkout/_partials/address-selector-block.tpl"
          name="id_address_delivery"
          addresses=$customer.addresses
          selected=$cart.id_address_delivery}
        {/block}
      </div>

      {block name="checkout_different_address_checkbox"}
        {include file="checkout/_partials/form-item-checkout-different-address.tpl"}
      {/block}

      <div id="checkout-address-invoice" class="address-selector -ps-hidden">
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
