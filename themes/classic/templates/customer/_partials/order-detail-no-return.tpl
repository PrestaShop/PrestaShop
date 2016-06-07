<div class="box">

<table id="order-products" class="table table-bordered">
  <thead class="thead-default">
    <tr>
      <th>{l s='Reference' d='Shop.Theme.Catalog'}</th>
      <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
      <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
      <th>{l s='Unit price' d='Shop.Theme.Catalog'}</th>
      <th class="text-xs-right">{l s='Total price' d='Shop.Theme.Catalog'}</th>
    </tr>
  </thead>

  {foreach from=$order.products item=product}
    <tr>
      <td>{$product.reference}</td>
      <td>{$product.name}</td>
      <td>{$product.quantity}</td>
      <td>{$product.price}</td>
      <td class="text-xs-right">{$product.total}</td>
    </tr>
    {if $product.customizations}
      {foreach $product.customizations  as $customization}
        <tr>
          <td colspan="2">
            <ul>
              {foreach from=$customization.fields item=field}
                {if $field.type == 'image'}
                  <li><img src="{$field.image.small.url}" alt=""></li>
                {elseif $field.type == 'text'}
                  <li>{$field.label} : {if (int)$field.id_module}{$field.text nofilter}{else}{$field.text}{/if}</li>
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
    {foreach $order.subtotals as $line}
      <tr class="text-xs-right line-{$line.type}">
        <td colspan="4">{$line.label}</td>
        <td>{$line.value}</td>
      </tr>
    {/foreach}

    <tr class="text-xs-right line-{$order.totals.total.type}">
      <td colspan="4">{$order.totals.total.label}</td>
      <td>{$order.totals.total.value}</td>
    </tr>
  </tfoot>
</table>

</div>
