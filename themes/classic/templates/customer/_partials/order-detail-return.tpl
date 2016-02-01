<form id="order-return-form" action="{$urls.pages.order_follow}" method="post">

  <table id="order-products" class="table table-bordered return">
    <thead class="thead-default">
      <tr>
        <th class="head-checkbox"><input type="checkbox"/></th>
        <th>{l s='Reference'}</th>
        <th>{l s='Product'}</th>
        <th>{l s='Quantity'}</th>
        <th>{l s='Returned'}</th>
        <th>{l s='Unit price'}</th>
        <th>{l s='Total price'}</th>
      </tr>
    </thead>

    {foreach from=$order.products item=product name=products}
      <tr>
        <td>
          {if !$product.customizedDatas}
            <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}" />
          {/if}
        </td>
        <td>{$product.product_reference}</td>
        <td>{$product.product_name}</td>
        <td class="qty">
          <div class="current">
            {$product.product_quantity}
          </div>
          <div class="select">
            {if !$product.customizedDatas}
              <select name="order_qte_input[{$product.id_order_detail}]" class="form-control">
            {else}
              <select name="order_qte_input[{$smarty.foreach.products.index}]" class="form-control">
            {/if}
                {section name=quantity start=1 loop=$product.product_quantity+1}
                  <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                {/section}
              </select>
          </div>
          <div class="clearfix"></div>
        </td>
        <td class="text-xs-right">{$product.qty_returned}</td>
        <td class="text-xs-right">{$product.unit_price}</td>
        <td class="text-xs-right">{$product.total_price}</td>
      </tr>
      {if $product.customizations}
        {foreach $product.customizations  as $customization}
          <tr>
            <td><input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{$customization.id_customization}" /></td>
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
            <td>
              {$customization.quantity}
              <span>
                <select name="customization_qty_input[{$customization.id_customization}]" class="form-control">
                  {section name=quantity start=1 loop=$customization.quantity+1}
                    <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                  {/section}
                </select>
              </span>
            </td>
            <td colspan="3"></td>
          </tr>
        {/foreach}
      {/if}
    {/foreach}

    <tfoot>
      {if $priceDisplay && $use_tax}
        <tr>
          <td colspan="2">{l s='Items (tax excl.)'}</td>
          <td colspan="5" class="text-xs-right">{$order.data.total_products}</td>
        </tr>
      {/if}
      <tr>
        <td colspan="2">{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
        <td colspan="5" class="text-xs-right">{$order.data.total_products_wt}</td>
      </tr>
      {if $order.data.total_discounts}
        <tr>
          <td colspan="2">{l s='Total vouchers'}</td>
          <td colspan="5" class="text-xs-right">{$order.data.total_discounts}</td>
        </tr>
      {/if}
      {if $order.data.total_wrapping}
      <tr>
        <td colspan="2">{l s='Total gift wrapping cost'}</td>
        <td colspan="5" class="text-xs-right">{$order.data.total_wrapping}</td>
      </tr>
      {/if}
      <tr>
        <td colspan="2">{l s='Shipping & handling'} {if $use_tax}{l s='(tax incl.)'}{/if}</td>
        <td colspan="5" class="text-xs-right">{$order.data.total_shipping}</td>
      </tr>
      <tr>
        <td colspan="2">{l s='Total'}</td>
        <td colspan="5" class="text-xs-right">{$order.data.total_paid}</td>
      </tr>
    </tfoot>
  </table>

  <header>
    <h3>{l s='Merchandise return'}</h3>
    <p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>
  </header>

  <section class="form-fields">
    <div class="form-group">
      <textarea cols="67" rows="3" name="returnText" class="form-control"></textarea>
    </div>
  </section>

  <footer class="form-footer">
    <input type="hidden" name="id_order" value="{$order.data.id}" />
    <button type="submit" name="submitReturnMerchandise" class="btn btn-primary">
      {l s='Make an RMA slip'}
    </button>
  </footer>
</form>
