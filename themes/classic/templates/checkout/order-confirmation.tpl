{extends file='page.tpl'}

{block name='page_content_container' prepend}
    <section id="content-hook_order_confirmation" class="card">
      <div class="card-block">
        <div class="row">
          <div class="col-md-12">

            {block name='order_confirmation_header'}
              <h3 class="h1 card-title">
                <i class="material-icons rtl-no-flip done">&#xE876;</i>{l s='Your order is confirmed' d='Shop.Theme.Checkout'}
              </h3>
            {/block}

            <p>
              {l s='An email has been sent to your mail address %email%.' d='Shop.Theme.Checkout' sprintf=['%email%' => $order_customer.email]}
              {if $order.details.invoice_url}
                {* [1][/1] is for a HTML tag. *}
                {l
                  s='You can also [1]download your invoice[/1]'
                  d='Shop.Theme.Checkout'
                  sprintf=[
                    '[1]' => "<a href='{$order.details.invoice_url}'>",
                    '[/1]' => "</a>"
                  ]
                }
              {/if}
            </p>

            {block name='hook_order_confirmation'}
              {$HOOK_ORDER_CONFIRMATION nofilter}
            {/block}

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
          {include
            file='checkout/_partials/order-confirmation-table.tpl'
            products=$order.products
            subtotals=$order.subtotals
            totals=$order.totals
            labels=$order.labels
            add_product_link=false
          }
        {/block}

        {block name='order_details'}
          <div id="order-details" class="col-md-4">
            <h3 class="h3 card-title">{l s='Order details' d='Shop.Theme.Checkout'}:</h3>
            <ul>
              <li id="order-reference-value">{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.reference]}</li>
              <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
              {if !$order.details.is_virtual}
                <li>
                  {l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]}<br>
                  <em>{$order.carrier.delay}</em>
                </li>
              {/if}
              {if $order.details.recyclable}
                <li>  
                  <em>{l s='You have given permission to receive your order in recycled packaging.' d="Shop.Theme.Customeraccount"}</em>
                </li>
              {/if}
            </ul>
          </div>
        {/block}

      </div>
    </div>
  </section>

  {block name='hook_payment_return'}
    {if ! empty($HOOK_PAYMENT_RETURN)}
    <section id="content-hook_payment_return" class="card definition-list">
      <div class="card-block">
        <div class="row">
          <div class="col-md-12">
            {$HOOK_PAYMENT_RETURN nofilter}
          </div>
        </div>
      </div>
    </section>
    {/if}
  {/block}

  {block name='customer_registration_form'}
    {if $order_customer.is_guest && !$registered_customer_exists}
      <div id="registration-form" class="card">
        <div class="card-block">
          <h4 class="h4">{l s='Save time on your next order, sign up now and enjoy:' d='Shop.Theme.Checkout'}</h4>
          <ul>
            <li> -{l s='Personalized and secure access' d='Shop.Theme.Customeraccount'}</li>
            <li> -{l s='Fast and easy checkout' d='Shop.Theme.Customeraccount'}</li>
            <li> -{l s='Easier merchandise return' d='Shop.Theme.Customeraccount'}</li>
          </ul>
          <form method="post">
            <div>
              <div class="form-group">
                <label class="form-control-label required" for="field-email">
                  {l s='Set your password:' d='Shop.Forms.Labels'}
                </label>
                <input type="password" class="form-control" data-validate="isPasswd" required name="password" value="">
              </div>
            </div>
            <footer class="form-footer">
              <input type="hidden" name="submitTransformGuestToCustomer" value="1">
              <input type="hidden" name="id_order" value="{$order.details.id}">
              <input type="hidden" name="email" value="{$order_customer.email}">
              <button class="btn btn-primary" type="submit">{l s='Create account' d='Shop.Theme.Actions'}</button>
            </footer>
          </form>
        </div>
      </div>
    {/if}
  {/block}

  {block name='hook_order_confirmation_1'}
    {hook h='displayOrderConfirmation1'}
  {/block}

  {block name='hook_order_confirmation_2'}
    <section id="content-hook-order-confirmation-footer">
      {hook h='displayOrderConfirmation2'}
    </section>
  {/block}
{/block}
