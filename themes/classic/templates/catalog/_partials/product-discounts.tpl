<section class="product-discounts">
  {if $product.quantity_discounts}
    <h3 class="h6 product-discounts-title">{l s='Volume discounts' d='Shop.Theme.Catalog'}</h3>
    <table class="table-product-discounts">
      <thead>
      <tr>
        <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
        <th>{if $display_discount_price}{l s='Price' d='Shop.Theme.Catalog'}{else}{l s='Discount' d='Shop.Theme.Catalog'}{/if}</th>
        <th>{l s='You Save' d='Shop.Theme.Catalog'}</th>
      </tr>
      </thead>
      <tbody>
      {foreach from=$product.quantity_discounts item='quantity_discount' name='quantity_discounts'}
        <tr data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.real_value}" data-discount-quantity="{$quantity_discount.quantity}">
          <td>{$quantity_discount.quantity}</td>
          <td>{$quantity_discount.discount}</td>
          <td>{l s='Up to %discount%' d='Shop.Theme.Catalog' sprintf=['%discount%' => $quantity_discount.save]}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
</section>
