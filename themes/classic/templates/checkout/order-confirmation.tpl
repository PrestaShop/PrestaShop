{extends file='page.tpl'}

{block name='page_content_container' prepend}
    <section id="content-hook_order_confirmation" class="card">
      <div class="card-block">
        <div class="row">
          <div class="col-md-12">
            <h3 class="h1 card-title"><i class="material-icons done">&#xE876;</i>{l s='Your order is confirmed'}</h3>
            <p>
              {l s='An email has been sent to your mail address %s.' sprintf=$customer.email}
              {if $url_to_invoice !== ''}{l s='You can also [1]download your invoice[/1]' tags=["<a href='{$url_to_invoice}'>"]}{/if}
            </p>
            {$HOOK_ORDER_CONFIRMATION nofilter}
          </div>
        </div>
      </div>
    </section>
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-order-confirmation card">
    <div class="card-block">
      <div class="row">

        {block name='order_confirmation_table'}
          {include file='checkout/_partials/order-confirmation-table.tpl' order=$order}
        {/block}

        <div id="order-details" class="col-md-4">
          <h3 class="h3 card-title">{l s='Order details'}</h3>
          <ul>
            <li>{l s='Order reference: %s' sprintf=$order.details.reference}</li>
            <li>{l s='Payment method: %s' sprintf=$order.details.payment}</li>
            <li>{l s='Shipping method: %s' sprintf=$order.carrier.name}</li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <section id="content-hook_payment_return" class="card definition-list">
    <div class="card-block">
      <div class="row">
        <div class="col-md-12">
          {$HOOK_PAYMENT_RETURN nofilter}
        </div>
      </div>
    </div>
  </section>

  {if $is_guest}
    <div id="registration-form" class="card">
      <div class="card-block">
        <h4 class="h4">{l s='Save time on your next order, sign up now'}</h4>
        {render file='customer/_partials/customer-form.tpl' ui=$register_form}
      </div>
    </div>
  {/if}

  {hook h='displayOrderConfirmation1'}

  <section id="content-hook-order-confirmation-footer">
    {hook h='displayOrderConfirmation2'}
  </section>
{/block}
