<section id="checkout-address-invoice" class="address-form -ps-hidden">
  <form action="{$urls.pages.order}" method="post">

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
      <input type="hidden" name="submitAddressInvoice" value="1">

      <button type="submit" id="submitAddressInvoice">
        {l s='Save'}
      </button>
    </footer>

  </form>
</section>
