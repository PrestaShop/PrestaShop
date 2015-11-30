<section id="checkout-address-delivery" class="address-form">
  <form action="{$urls.pages.order}" method="post">

    <section class="form-fields">

      {block name="address_form_fields"}
        {include file="customer/_partials/address-form-fields.tpl"
        address=$address
        countries=$countries}
      {/block}

      {block name="checkout_different_address_checkbox"}
        {include file="checkout/_partials/form-item-checkout-different-address.tpl"}
      {/block}

    </section>

    <footer class="form-footer">
      <input type="hidden" class="hidden" name="address_role" value="delivery">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="submitAddressDelivery" value="1">

      <button type="submit" id="submitAddressDelivery">
        {l s='Save'}
      </button>
    </footer>

  </form>
</section>
