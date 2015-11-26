<section id="checkout-address-delivery" class="address-form">
  <form action="{$urls.pages.order}" method="post">

    <section class="form-fields">

      {block name="address_form_fields"}
        {include file="customer/_partials/address-form-fields.tpl"
        address=$address
        countries=$countries}
      {/block}

      {block name="use_different_address"}
        <label for="use_different_address">
          <input type="checkbox" id="use_different_address" name="use_different_address">
          {l s='Use a different address for invoicing'}
        </label>
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
