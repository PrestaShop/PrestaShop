<div class="box">

<table id="order-products" class="table table-bordered">
  <thead class="thead-default">
    <tr>
      <th>{l s='Reference'}</th>
      <th>{l s='Product'}</th>
      <th>{l s='Quantity'}</th>
      <th>{l s='Unit price'}</th>
      <th>{l s='Total price'}</th>
    </tr>
  </thead>

  {foreach from=$order.products item=product}
    <tr>
      <td>{$product.reference}</td>
      <td>{$product.name}</td>
      <td class="text-xs-right">{$product.quantity}</td>
      <td class="text-xs-right">{$product.price}</td>
      <td class="text-xs-right">{$product.total}</td>
    </tr>
    {if $product.customizations}
      {foreach $product.customizations  as $customization}
        <tr>
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
          <td colspan="2"></td>
        </tr>
      {/foreach}
    {/if}
  {/foreach}

  <tfoot>
    {if $priceDisplay && $use_tax}
    <tr>
        <td>{l s='Items (tax excl.)'}</td>
        <td colspan="4" class="text-xs-right">{$order.total.amount}</td>
    </tr>
    {/if}
    {if isset($order.subtotals.tax)}
    <tr>
      <td>{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
      <td colspan="4" class="text-xs-right">{$order.subtotals.tax.amount}</td>
    </tr>
    {/if}
    {if $order.subtotals.discounts.amount}
      <tr>
        <td>{l s='Total vouchers'}</td>
        <td colspan="4" class="text-xs-right">- {$order.subtotals.discounts.amount}</td>
      </tr>
    {/if}
    {if isset($order.subtotals.gift_wrapping)}
    <tr>
      <td>{l s='Total gift wrapping cost'}</td>
      <td colspan="4" class="text-xs-right">{$order.subtotals.gift_wrapping.amount}</td>
    </tr>
    {/if}
    <tr>
      <td>{l s='Shipping & handling'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
      <td colspan="4" class="text-xs-right">{$order.subtotals.shipping.amount}</td>
    </tr>
    <tr>
      <td>{l s='Total'}</td>
      <td colspan="4" class="text-xs-right">{$order.total.total_paid.amount}</td>
    </tr>
  </tfoot>
</table>

</div>
