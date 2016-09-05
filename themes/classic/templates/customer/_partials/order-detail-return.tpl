<div class="box">

<form id="order-return-form" action="{$urls.pages.order_follow}" method="post">

  <table id="order-products" class="table table-bordered return">
    <thead class="thead-default">
      <tr>
        <th class="head-checkbox"><input type="checkbox"/></th>
        <th>{l s='Reference' d='Shop.Theme.Catalog'}</th>
        <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
        <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
        <th>{l s='Returned' d='Shop.Theme.Catalog'}</th>
        <th>{l s='Unit price' d='Shop.Theme.Catalog'}</th>
        <th>{l s='Total price' d='Shop.Theme.Catalog'}</th>
      </tr>
    </thead>

    {foreach from=$order.products item=product name=products}
      <tr>
        <td>
          {if !$product.customizations}
            <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}">
          {else}
            {foreach $product.customizations  as $customization}
              <input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{$customization.id_customization}">
            {/foreach}
          {/if}
        </td>
        <td>{$product.reference}</td>
        <td>{$product.name}
          {if $product.customizations}
            <br />
            {foreach $product.customizations  as $customization}
            <ul>
              {foreach from=$customization.fields item=field}
                {if $field.type == 'image'}
                  <li><img src="{$field.image.small.url}" alt=""></li>
                {elseif $field.type == 'text'}
                  <li>{$field.label} : {if (int)$field.id_module}{$field.text nofilter}{else}{$field.text}{/if}</li>
                {/if}
              {/foreach}
            </ul>
            {/foreach}
          {/if}
        </td>
        <td class="qty">
          {if !$product.customizations}
            <div class="current">
              {$product.quantity}
            </div>
            <div class="select">
              <select name="order_qte_input[{$product.id_order_detail}]" class="form-control form-control-select">
                {section name=quantity start=1 loop=$product.quantity+1-$product.qty_returned}
                  <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                {/section}
              </select>
            </div>
          {else}
            {foreach $product.customizations  as $customization}
               <div class="current">
                {$customization.quantity}
              </div>
              <div class="select">
                <select
                  name="customization_qty_input[{$customization.id_customization}]"
                  class="form-control form-control-select"
                >
                  {section name=quantity start=1 loop=$customization.quantity+1}
                    <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                  {/section}
                </select>
              </div>
            {/foreach}
            <div class="clearfix"></div>
          {/if}
        </td>
        <td class="text-xs-right">{$product.qty_returned}</td>
        <td class="text-xs-right">{$product.price}</td>
        <td class="text-xs-right">{$product.total}</td>
      </tr>
    {/foreach}

    <tfoot>
      {foreach $order.subtotals as $line}
        <tr class="text-xs-right line-{$line.type}">
          <td colspan="5">{$line.label}</td>
          <td colspan="2">{$line.value}</td>
        </tr>
      {/foreach}

      <tr class="text-xs-right line-{$order.totals.total.type}">
        <td colspan="5">{$order.totals.total.label}</td>
        <td colspan="2">{$order.totals.total.value}</td>
      </tr>
    </tfoot>
  </table>

  <header>
    <h3>{l s='Merchandise return' d='Shop.Theme.CustomerAccount'}</h3>
    <p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.' d='Shop.Theme.CustomerAccount'}</p>
  </header>

  <section class="form-fields">
    <div class="form-group">
      <textarea cols="67" rows="3" name="returnText" class="form-control"></textarea>
    </div>
  </section>

  <footer class="form-footer">
    <input type="hidden" name="id_order" value="{$order.details.id}">
    <button class="form-control-submit btn btn-primary" type="submit" name="submitReturnMerchandise">
      {l s='Request a return' d='Shop.Theme.CustomerAccount'}
    </button>
  </footer>
</form>

</div>
