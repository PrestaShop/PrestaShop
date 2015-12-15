<section id="checkout-address-invoice" class="address-form">
    <section class="form-fields">
      {block name="address_form_fields"}
        {include file="customer/_partials/address-form-fields.tpl"
        address=$address
        countries=$countries}
      {/block}
    </section>

    <footer class="form-footer">
      <input type="hidden" class="hidden" name="address_role" value="invoice">
      <input type="hidden" name="token" value="{$token}">
      <button type="submit" name="saveAddress" value="invoice">
        {l s='Save'}
      </button>
    </footer>
</section>
