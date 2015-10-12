<form action="{$urls.pages.order_follow}" method="post">

  <table>
    <thead>
      <tr>
        <th><input type="checkbox" /></th>
        <th>{l s='Reference'}</th>
        <th>{l s='Product'}</th>
        <th>{l s='Quantity'}</th>
        <th>{l s='Returned'}</th>
        <th>{l s='Unit price'}</th>
        <th>{l s='Total price'}</th>
      </tr>
    </thead>

    {foreach from=$products item=product}
      <tr>
        <td>
          {if !$product.customizedDatas}
            <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}" />
          {/if}
        </td>
        <td>{$product.product_reference}</td>
        <td>{$product.product_name}</td>
        <td>{$product.product_quantity}</td>
        <td>{$product.qty_returned}</td>
        <td>{$product.unit_price}</td>
        <td>{$product.total_price}</td>
      </tr>
      {if $product.customizations}
        {foreach $product.customizations  as $customization}
          <tr>
            <td><input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{*$customizationId*}" /></td>
            <td colspan="2">
              <ul>
                {foreach from=$customization.fields item=field}
                  {if $field.type == 'image'}
                    <li><img src="{$field.image.small.url}" alt="" /></li>
                  {elseif $field.type == 'text'}
                    <li>{$field.label} : {$field.text}</li>
                  {/if}
                {/foreach}
              </ul>
            </td>
            <td>{$customization.quantity}</td>
            <td colspan="3"></td>
          </tr>
        {/foreach}
      {/if}
    {/foreach}

    <tfoot>
      {if $priceDisplay && $use_tax}
        <tr>
          <td colspan="2">{l s='Items (tax excl.)'}</td>
          <td colspan="5">{$order.total_products}</td>
        </tr>
      {/if}
      <tr>
        <td colspan="2">{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
        <td colspan="5">{$order.total_products_wt}</td>
      </tr>
      {if $order.total_discounts}
        <tr>
          <td colspan="2">{l s='Total vouchers'}</td>
          <td colspan="5">{$order.total_discounts}</td>
        </tr>
      {/if}
      {if $order.total_wrapping}
      <tr>
        <td colspan="2">{l s='Total gift wrapping cost'}</td>
        <td colspan="5">{$order->total_wrapping}</td>
      </tr>
      {/if}
      <tr>
        <td colspan="2">{l s='Shipping & handling'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
        <td colspan="5">{$order.total_shipping}</td>
      </tr>
      <tr>
        <td colspan="2">{l s='Total'}</td>
        <td colspan="5">{$order.total_paid}</td>
      </tr>
    </tfoot>
  </table>

  <header>
    <h1 class="h3">{l s='Merchandise return'}</h1>
    <p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>
  </header>

  <section class="form-fields">

    <label>
      <textarea cols="67" rows="3" name="returnText"></textarea>
    </label>

  </section>

  <footer class="form-footer">
    <input type="hidden" name="id_order" value="{$order.id}" />
    <button type="submit" name="submitReturnMerchandise">
      {l s='Make an RMA slip'}
    </button>
  </footer>
</form>
