{block name='order-items-table-head'}
<div id="order-items" class="col-md-8">
  <h3 class="card-title h3">{l s='Order items' d='Shop.Theme.Checkout'}</h3>
{/block}
  <div class="order-confirmation-table">
    <table class="table">
      {foreach from=$products item=product}
        <div class="order-line row">
          <div class="col-sm-2 col-xs-3">
            <span class="image">
              <img src="{$product.cover.medium.url}" />
            </span>
          </div>
          <div class="col-sm-4 col-xs-9 details">
            {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
              <span>{$product.name}</span>
            {if $add_product_link}</a>{/if}
            {if $product.customizations|count}
              {foreach from=$product.customizations item="customization"}
                <div class="customizations">
                  <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                </div>
                <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                      </div>
                      <div class="modal-body">
                        {foreach from=$customization.fields item="field"}
                          <div class="product-customization-line row">
                            <div class="col-sm-3 col-xs-4 label">
                              {$field.label}
                            </div>
                            <div class="col-sm-9 col-xs-8 value">
                              {if $field.type == 'text'}
                                {if (int)$field.id_module}
                                  {$field.text nofilter}
                                {else}
                                  {$field.text}
                                {/if}
                              {elseif $field.type == 'image'}
                                <img src="{$field.image.small.url}">
                              {/if}
                            </div>
                          </div>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                </div>
              {/foreach}
            {/if}
            {hook h='displayProductPriceBlock' product=$product type="unit_price"}
          </div>
          <div class="col-sm-6 col-xs-12 qty">
            <div class="row">
              <div class="col-xs-5 text-sm-right text-xs-left">{$product.price}</div>
              <div class="col-xs-2">{$product.quantity}</div>
              <div class="col-xs-5 text-xs-right bold">{$product.total}</div>
            </div>
          </div>
        </div>
      {/foreach}
    <hr />
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
