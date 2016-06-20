<section id="order-summary-content" class="page-content page-order-confirmation">
  <div class="row">
    <div class="col-md-12">
      <h4 class="h4 black">{l s='Please check your order before payment' d='Shop.Theme.Checkout'}</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h4 class="h4">
      {l s='Addresses' d='Shop.Theme.Checkout'}
        <span class="step-edit step-to-addresses"><i class="material-icons edit">mode_edit</i> edit</span>
      </h4>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="card noshadow">
        <div class="card-block">
          <h4 class="h4 black addresshead">{l s='My Delivery Address' d='Shop.Theme.Checkout'}</h4>
          {$customer.addresses[$cart.id_address_delivery]['formatted'] nofilter}
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card noshadow">
        <div class="card-block">
          <h4 class="h4 black addresshead">{l s='My Invoice Address' d='Shop.Theme.Checkout'}</h4>
          {$customer.addresses[$cart.id_address_invoice]['formatted'] nofilter}
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h4 class="h4">
      {l s='Shipping Method' d='Shop.Theme.Checkout'}
        <span class="step-edit step-to-delivery"><i class="material-icons edit">mode_edit</i> edit</span>
      </h4>

      <div class="col-md-12">
        <div class="row">
          <div class="col-md-2">
            <div class="logo-container">
              {if $selected_delivery_option.logo}
                <img src="{$selected_delivery_option.logo}" alt="{$selected_delivery_option.name}">
              {else}
                &nbsp;
              {/if}
            </div>
          </div>
          <div class="col-md-4">
            <span class="carrier-name">{$selected_delivery_option.name}</span>
          </div>
          <div class="col-md-4">
            <span class="carrier-delay">{$selected_delivery_option.delay}</span>
          </div>
          <div class="col-md-2">
            <span class="carrier-price">{$selected_delivery_option.price}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {block name='order_confirmation_table'}
      {include file='checkout/_partials/order-final-summary-table.tpl'
         products=$cart.products
         products_count=$cart.products_count
         subtotals=$cart.subtotals
         totals=$cart.totals
         labels=$cart.labels
       }
    {/block}
  </div>
</section>
