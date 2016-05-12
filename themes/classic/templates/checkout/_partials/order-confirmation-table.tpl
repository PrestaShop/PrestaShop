
<div id="order-items" class="col-md-8">
  <h3 class="card-title h3">{l s='Order items'}</h3>
  <table class="table">
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
    {foreach $order.subtotals as $subtotal}
      <tr>
        <td>{$subtotal.label}</td>
        <td>{$subtotal.value}</td>
      </tr>
    {/foreach}

    <tr class="font-weight-bold">
      <td><span class="text-uppercase">{$order.totals.total.label}</span> {$order.tax_label}</td>
      <td>{$order.totals.total.value}</td>
    </tr>
  </table>
</div>
