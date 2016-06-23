{block name='order-items-table-head'}
<div id="order-items" class="col-md-8">
  <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
{/block}
  <table class="table">
    {foreach from=$products item=product}
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
                    <span class="product-quantity">{l s='Quantity' d='Shop.Theme.Checkout'} {$customization.quantity}</span>
                    <ul>
                      {foreach from=$customization.fields item="field"}
                        <li>
                          <label>{$field.label}</label>
                          {if $field.type == 'text'}
                            {if (int)$field.id_module}
                              <span>{$field.text nofilter}</span>
                            {else}
                              <span>{$field.text}</span>
                            {/if}
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
    {foreach $subtotals as $subtotal}
      {if $subtotal.type !== 'tax'}
        <tr>
          <td>{$subtotal.label}</td>
          <td>{$subtotal.value}</td>
        </tr>
      {/if}
    {/foreach}

    {if $subtotals.tax.label !== null}
      <tr class="sub">
        <td>{$subtotals.tax.label}</td>
        <td>{$subtotals.tax.value}</td>
      </tr>
    {/if}

    <tr class="font-weight-bold">
      <td><span class="text-uppercase">{$totals.total.label}</span> {$labels.tax_short}</td>
      <td>{$totals.total.value}</td>
    </tr>
  </table>
</div>
