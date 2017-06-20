{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='order_products_table'}
  <form id="order-return-form" action="{$urls.pages.order_follow}" method="post">

    <div class="box hidden-sm-down">
      <table id="order-products" class="table table-bordered return">
        <thead class="thead-default">
          <tr>
            <th class="head-checkbox"><input type="checkbox"/></th>
            <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
            <th>{l s='Quantity' d='Shop.Theme.Catalog'}</th>
            <th>{l s='Returned' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Unit price' d='Shop.Theme.Catalog'}</th>
            <th>{l s='Total price' d='Shop.Theme.Catalog'}</th>
          </tr>
        </thead>
        {foreach from=$order.products item=product name=products}
          <tr>
            <td>
              {if !$product.customizations}
                <span id="_desktop_product_line_{$product.id_order_detail}">
                <input type="checkbox" id="cb_{$product.id_order_detail}" name="ids_order_detail[{$product.id_order_detail}]" value="{$product.id_order_detail}">
              </span>
              {else}
                {foreach $product.customizations  as $customization}
                  <span id="_desktop_product_customization_line_{$product.id_order_detail}_{$customization.id_customization}">
                  <input type="checkbox" id="cb_{$product.id_order_detail}" name="customization_ids[{$product.id_order_detail}][]" value="{$customization.id_customization}">
                </span>
                {/foreach}
              {/if}
            </td>
            <td>
              <strong>{$product.name}</strong><br/>
              {if $product.reference}
                {l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}<br/>
              {/if}
              {if $product.customizations}
                {foreach from=$product.customizations item="customization"}
                  <div class="customization">
                    <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                  </div>
                  <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization}">
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
                  </div>
                {/foreach}
              {/if}
            </td>
            <td class="qty">
              {if !$product.customizations}
                <div class="current">
                  {$product.quantity}
                </div>
                {if $product.quantity > $product.qty_returned}
                  <div class="select" id="_desktop_return_qty_{$product.id_order_detail}">
                    <select name="order_qte_input[{$product.id_order_detail}]" class="form-control form-control-select">
                      {section name=quantity start=1 loop=$product.quantity+1-$product.qty_returned}
                        <option value="{$smarty.section.quantity.index}">{$smarty.section.quantity.index}</option>
                      {/section}
                    </select>
                  </div>
                {/if}
              {else}
                {foreach $product.customizations as $customization}
                  <div class="current">
                    {$customization.quantity}
                  </div>
                  <div class="select" id="_desktop_return_qty_{$product.id_order_detail}_{$customization.id_customization}">
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
            <td class="text-xsright">{$product.qty_returned}</td>
            <td class="text-xsright">{$product.price}</td>
            <td class="text-xsright">{$product.total}</td>
          </tr>
        {/foreach}
        <tfoot>
          {foreach $order.subtotals as $line}
            {if $line.value}
              <tr class="text-xsright line-{$line.type}">
                <td colspan="5">{$line.label}</td>
                <td colspan="2">{$line.value}</td>
              </tr>
            {/if}
          {/foreach}
          <tr class="text-xsright line-{$order.totals.total.type}">
            <td colspan="5">{$order.totals.total.label}</td>
            <td colspan="2">{$order.totals.total.value}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="order-items hidden-md-up box">
      {foreach from=$order.products item=product}
        <div class="order-item">
          <div class="row">
            <div class="checkbox">
              {if !$product.customizations}
                <span id="_mobile_product_line_{$product.id_order_detail}"></span>
              {else}
                {foreach $product.customizations  as $customization}
                  <span id="_mobile_product_customization_line_{$product.id_order_detail}_{$customization.id_customization}"></span>
                {/foreach}
              {/if}
            </div>
            <div class="content">
              <div class="row">
                <div class="col-sm-5 desc">
                  <div class="name">{$product.name}</div>
                  {if $product.reference}
                    <div class="ref">{l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference}</div>
                  {/if}
                  {if $product.customizations}
                    {foreach $product.customizations as $customization}
                      <div class="customization">
                        <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                      </div>
                      <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization}">
                      </div>
                    {/foreach}
                  {/if}
                </div>
                <div class="col-sm-7 qty">
                  <div class="row">
                    <div class="col-xs-4 text-smleft text-xsleft">
                      {$product.price}
                    </div>
                    <div class="col-xs-4">
                      {if $product.customizations}
                        {foreach $product.customizations as $customization}
                          <div class="q">{l s='Quantity' d='Shop.Theme.Catalog'}: {$customization.quantity}</div>
                          <div class="s" id="_mobile_return_qty_{$product.id_order_detail}_{$customization.id_customization}"></div>
                        {/foreach}
                      {else}
                        <div class="q">{l s='Quantity' d='Shop.Theme.Catalog'}: {$product.quantity}</div>
                        {if $product.quantity > $product.qty_returned}
                          <div class="s" id="_mobile_return_qty_{$product.id_order_detail}"></div>
                        {/if}
                      {/if}
                      {if $product.qty_returned > 0}
                        <div>{l s='Returned' d='Shop.Theme.Customeraccount'}: {$product.qty_returned}</div>
                      {/if}
                    </div>
                    <div class="col-xs-4 text-xsright">
                      {$product.total}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      {/foreach}
    </div>
    <div class="order-totals hidden-md-up box">
      {foreach $order.subtotals as $line}
        {if $line.value}
          <div class="order-total row">
            <div class="col-xs-8"><strong>{$line.label}</strong></div>
            <div class="col-xs-4 text-xsright">{$line.value}</div>
          </div>
        {/if}
      {/foreach}
      <div class="order-total row">
        <div class="col-xs-8"><strong>{$order.totals.total.label}</strong></div>
        <div class="col-xs-4 text-xsright">{$order.totals.total.value}</div>
      </div>
    </div>

    <div class="box">
      <header>
        <h3>{l s='Merchandise return' d='Shop.Theme.Customeraccount'}</h3>
        <p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.' d='Shop.Theme.Customeraccount'}</p>
      </header>
      <section class="form-fields">
        <div class="form-group">
          <textarea cols="67" rows="3" name="returnText" class="form-control"></textarea>
        </div>
      </section>
      <footer class="form-footer">
        <input type="hidden" name="id_order" value="{$order.details.id}">
        <button class="form-control-submit btn btn-primary" type="submit" name="submitReturnMerchandise">
          {l s='Request a return' d='Shop.Theme.Customeraccount'}
        </button>
      </footer>
    </div>

  </form>
{/block}
