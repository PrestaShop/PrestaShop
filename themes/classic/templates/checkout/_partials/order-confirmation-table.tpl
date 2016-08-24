{block name='order-items-table-head'}
<div id="order-items" class="col-md-8">
  <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
{/block}
  <div class="order-confirmation-table">
    <table class="table">
      {foreach from=$products item=product}

        <div class="row">
          <div class="col-sm-2 col-xs-3">
            <span class="thumb-mask product-image media-middle">
              <div class="mask">
                <img class="" src="{$product.cover.medium.url}">
              </div>
            </span>
          </div>
          <div class="col-sm-4 col-xs-9 details">
            {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
              <span class="bold">{$product.name}</span>
            {if $add_product_link}</a>{/if}
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
          </div>
          <div class="col-sm-6 col-xs-12">
            <div class="row">
              <div class="col-xs-4 text-xs-left">{$product.price}</div>
              <div class="col-xs-4">{$product.quantity}</div>
              <div class="col-xs-4 text-xs-right bold">{$product.total}</div>
            </div>
          </div>
        </div>
      {/foreach}


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
</div>
