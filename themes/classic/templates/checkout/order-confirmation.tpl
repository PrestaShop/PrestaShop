{extends file='page.tpl'}

{block name='page_content_container' prepend}
    <section id="content-hook_order_confirmation">
      <div class="card">
        <h3 class="h1 card-title"><i class="material-icons done">&#xE876;</i>{l s='Your order is confirmed'}</h3>
        {if $url_to_invoice !== ''}
          <div class="card-block">
            {l s='An email has been sent to your mail address %s.' sprintf=$customer.email}
            {l s='You can also [1]download your invoice[/1]' tags=["<a href='{$url_to_invoice}'>"]}
          </div>
        {/if}
        {$HOOK_ORDER_CONFIRMATION nofilter}
      </div>
    </section>
{/block}

{block name='page_content_container'}
    <section id="content" class="page-content page-order-confirmation card">
        <div class="row">
           <div id="order-items" class="col-md-8">
              <h3 class="card-title h3">{l s='Order items'}</h3>
              <table>
                  {foreach from=$order.products item=product}
                      <tr>
                          <td>
                          <span class="thumb-mask product-image media-middle">
                            <div class="mask">
                              <img class="" src="{$product.cover.medium.url}">
                            </div>
                        </span>
                        </td>
                        <td>
                            {$product.name}
                            {foreach from=$product.attributes key="attribute" item="value"}
                                - <span class="value">{$value}</span>
                            {/foreach}
                            {if $product.customizations|count}
                                <div class="customizations">
                                    <ul>
                                        {foreach from=$product.customizations item="customization"}
                                            <li>
                                                <span class="product-quantity">{l s='Quantity'} {$customization.quantity}</span>
                                                <ul>
                                                    {foreach from=$customization.fields item="field"}
                                                        <li>
                                                            <label>{$field.label}</label>
                                                            {if $field.type == 'text'}
                                                                <span>{$field.text}</span>
                                                            {else if $field.type == 'image'}
                                                                <img src="{$field.image.small.url}">
                                                            {/if}
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}
                            {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                        </td>
                        <td>{$product.price}</td>
                        <td>{$product.quantity}</td>
                        <td>{$product.total}</td>
                    </tr>
                {/foreach}
            </table>

            <hr>

            <table>
                {if isset($order.subtotals.discounts)}
                <tr>
                    <td>{l s='Promo code'}</td>
                    <td>- {$order.subtotals.discounts.value}</td>
                </tr>
                {/if}
                <tr>
                    <td>{l s='Shipping cost'}</td>
                    <td>{$order.subtotals.shipping.value}</td>
                </tr>
                {if isset($order.subtotals.tax) }
                    <tr>
                        <td>{l s='Taxes'}</td>
                        <td>{$order.subtotals.tax.value}</td>
                    </tr>
                {/if}
                <tr>
                    <td class="text-uppercase"><strong>{l s='Total'}</strong></td>
                    <td><strong>{$order.total.value}</strong></td>
                </tr>
            </table>
          </div>
          <div id="order-details" class="col-md-4">
            <h3 class="h3 card-title">{l s='Order details'}</h3>
            <ul>
              <li>{l s='Order reference %s' sprintf=$order.details.reference}</li>
              <li>{l s='Payment method %s' sprintf=$order.details.payment}</li>
              <li>{l s='Shipping method %s' sprintf=$order.carrier.name}</li>
            </ul>
          </div>
      </section>
      <section id="content-hook_payment_return" class="card definition-list">
          {$HOOK_PAYMENT_RETURN nofilter}
      </section>

        {if $is_guest}
          <div id="registration-form" class="card">
              <h4 class="h4">{l s='Save time on your next order, sign up now'}</h4>
              {render file='customer/_partials/customer-form.tpl' ui=$register_form}
          </div>
        {/if}

        {hook h='displayOrderConfirmation1'}

      </div>
      <section id="content-hook-order-confirmation-footer">
        {hook h='displayOrderConfirmation2'}
      </section>
{/block}
