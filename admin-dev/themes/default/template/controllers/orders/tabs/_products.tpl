    <div class="row" id="start_products">
        <div class="col-lg-12">
            <form class="container-command-top-spacing" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.'}', '{l s='Quantity to cancel is greater than quantity available.'}');">
                <input type="hidden" name="id_order" value="{$order->id}" />
                <div style="display: none">
                    <input type="hidden" value="{$order->getWarehouseList()|implode}" id="warehouse_list" />
                </div>

                    <div class="panel-heading">
                        <i class="icon-shopping-cart"></i>
                        {l s='Products'} <span class="badge">{$products|@count}</span>
                    </div>
                    <div id="refundForm">
                    <!--
                        <a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Process a standard refund'}" /> {l s='Process a standard refund'}</a>
                        <a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Process a partial refund'}" /> {l s='Process a partial refund'}</a>
                    -->
                    </div>

                    {capture "TaxMethod"}
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                            {l s='tax excluded.'}
                        {else}
                            {l s='tax included.'}
                        {/if}
                    {/capture}
                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                        <input type="hidden" name="TaxMethod" value="0">
                    {else}
                        <input type="hidden" name="TaxMethod" value="1">
                    {/if}
                    <div class="table-responsive">
                        <table class="table" id="orderProducts">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><span class="title_box ">{l s='Product'}</span></th>
                                    <th>
                                        <span class="title_box ">{l s='Unit Price'}</span>
                                        <small class="text-muted">{$smarty.capture.TaxMethod}</small>
                                    </th>
                                    <th class="text-center"><span class="title_box ">{l s='Qty'}</span></th>
                                    {if $display_warehouse}<th><span class="title_box ">{l s='Warehouse'}</span></th>{/if}
                                    {if ($order->hasBeenPaid())}<th class="text-center"><span class="title_box ">{l s='Refunded'}</span></th>{/if}
                                    {if ($order->hasBeenDelivered() || $order->hasProductReturned())}
                                        <th class="text-center"><span class="title_box ">{l s='Returned'}</span></th>
                                    {/if}
                                    {if $stock_management}<th class="text-center"><span class="title_box ">{l s='Available quantity'}</span></th>{/if}
                                    <th>
                                        <span class="title_box ">{l s='Total'}</span>
                                        <small class="text-muted">{$smarty.capture.TaxMethod}</small>
                                    </th>
                                    <th style="display: none;" class="add_product_fields"></th>
                                    <th style="display: none;" class="edit_product_fields"></th>
                                    <th style="display: none;" class="standard_refund_fields">
                                        <i class="icon-minus-sign"></i>
                                        {if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
                                            {l s='Return'}
                                        {elseif ($order->hasBeenPaid())}
                                            {l s='Refund'}
                                        {else}
                                            {l s='Cancel'}
                                        {/if}
                                    </th>
                                    <th style="display:none" class="partial_refund_fields">
                                        <span class="title_box ">{l s='Partial refund'}</span>
                                    </th>
                                    {if !$order->hasBeenDelivered()}
                                    <th></th>
                                    {/if}
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$products item=product key=k}
                                {* Include customized datas partial *}
                                {include file='controllers/orders/_customized_data.tpl'}
                                {* Include product line partial *}
                                {include file='controllers/orders/_product_line.tpl'}
                            {/foreach}
                            {if $can_edit}
                                {include file='controllers/orders/_new_product.tpl'}
                            {/if}
                            </tbody>
                        </table>
                    </div>

                    {if $can_edit}
                    <div class="row-margin-bottom row-margin-top order_action">
                    {if !$order->hasBeenDelivered()}
                        <button type="button" id="add_product" class="btn btn-default">
                            <i class="icon-plus-sign"></i>
                            {l s='Add a product'}
                        </button>
                    {/if}
                        <button id="add_voucher" class="btn btn-default" type="button" >
                            <i class="icon-ticket"></i>
                            {l s='Add a new discount'}
                        </button>
                    </div>
                    {/if}
                    <div class="clear">&nbsp;</div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="alert alert-warning">
                                {l s='For this customer group, prices are displayed as: [1]%s[/1]' sprintf=[$smarty.capture.TaxMethod] tags=['<strong>']}
                                {if !Configuration::get('PS_ORDER_RETURN')}
                                    <br/><strong>{l s='Merchandise returns are disabled'}</strong>
                                {/if}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="panel panel-vouchers" style="{if !sizeof($discounts)}display:none;{/if}">
                                {if (sizeof($discounts) || $can_edit)}
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <span class="title_box ">
                                                        {l s='Discount name'}
                                                    </span>
                                                </th>
                                                <th>
                                                    <span class="title_box ">
                                                        {l s='Value'}
                                                    </span>
                                                </th>
                                                {if $can_edit}
                                                <th></th>
                                                {/if}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$discounts item=discount}
                                            <tr>
                                                <td>{$discount['name']}</td>
                                                <td>
                                                {if $discount['value'] != 0.00}
                                                    -
                                                {/if}
                                                {displayPrice price=$discount['value'] currency=$currency->id}
                                                </td>
                                                {if $can_edit}
                                                <td>
                                                    <a href="{$current_index}&amp;submitDeleteVoucher&amp;id_order_cart_rule={$discount['id_order_cart_rule']}&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                                                        <i class="icon-minus-sign"></i>
                                                        {l s='Delete voucher'}
                                                    </a>
                                                </td>
                                                {/if}
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="current-edit" id="voucher_form" style="display:none;">
                                    {include file='controllers/orders/_discount_form.tpl'}
                                </div>
                                {/if}
                            </div>
                            <div class="panel panel-total">
                                <div class="table-responsive">
                                    <table class="table">
                                        {* Assign order price *}
                                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                                            {assign var=order_product_price value=($order->total_products)}
                                            {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                                            {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                                        {else}
                                            {assign var=order_product_price value=$order->total_products_wt}
                                            {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                                            {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                                        {/if}
                                        <tr id="total_products">
                                            <td class="text-right">{l s='Products:'}</td>
                                            <td class="amount text-right nowrap">
                                                {displayPrice price=$order_product_price currency=$currency->id}
                                            </td>
                                            <td class="partial_refund_fields current-edit" style="display:none;"></td>
                                        </tr>
                                        <tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
                                            <td class="text-right">{l s='Discounts'}</td>
                                            <td class="amount text-right nowrap">
                                                -{displayPrice price=$order_discount_price currency=$currency->id}
                                            </td>
                                            <td class="partial_refund_fields current-edit" style="display:none;"></td>
                                        </tr>
                                        <tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
                                            <td class="text-right">{l s='Wrapping'}</td>
                                            <td class="amount text-right nowrap">
                                                {displayPrice price=$order_wrapping_price currency=$currency->id}
                                            </td>
                                            <td class="partial_refund_fields current-edit" style="display:none;"></td>
                                        </tr>
                                        <tr id="total_shipping">
                                            <td class="text-right">{l s='Shipping'}</td>
                                            <td class="amount text-right nowrap" >
                                                {displayPrice price=$order_shipping_price currency=$currency->id}
                                            </td>
                                            <td class="partial_refund_fields current-edit" style="display:none;">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        {$currency->prefix}
                                                        {$currency->suffix}
                                                    </div>
                                                    <input type="text" name="partialRefundShippingCost" value="0" />
                                                </div>
                                                <p class="help-block"><i class="icon-warning-sign"></i> {l s='(%s)' sprintf=$smarty.capture.TaxMethod}</p>
                                            </td>
                                        </tr>
                                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                                        <tr id="total_taxes">
                                            <td class="text-right">{l s='Taxes'}</td>
                                            <td class="amount text-right nowrap" >{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$currency->id}</td>
                                            <td class="partial_refund_fields current-edit" style="display:none;"></td>
                                        </tr>
                                        {/if}
                                        {assign var=order_total_price value=$order->total_paid_tax_incl}
                                        <tr id="total_order">
                                            <td class="text-right"><strong>{l s='Total'}</strong></td>
                                            <td class="amount text-right nowrap">
                                                <strong>{displayPrice price=$order_total_price currency=$currency->id}</strong>
                                            </td>
                                            <td class="partial_refund_fields current-edit" style="display:none;"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: none;" class="standard_refund_fields form-horizontal panel">
                        <div class="form-group">
                            {if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
                            <p class="checkbox">
                                <label for="reinjectQuantities">
                                    <input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" />
                                    {l s='Re-stock products'}
                                </label>
                            </p>
                            {/if}
                            {if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
                            <p class="checkbox">
                                <label for="generateCreditSlip">
                                    <input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
                                    {l s='Generate a credit slip'}
                                </label>
                            </p>
                            <p class="checkbox">
                                <label for="generateDiscount">
                                    <input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
                                    {l s='Generate a voucher'}
                                </label>
                            </p>
                            <p class="checkbox" id="spanShippingBack" style="display:none;">
                                <label for="shippingBack">
                                    <input type="checkbox" id="shippingBack" name="shippingBack" />
                                    {l s='Repay shipping costs'}
                                </label>
                            </p>
                            {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
                            <br/><p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund:'}</p>
                            <p class="radio">
                                <label id="lab_refund_total_1" for="refund_total_1">
                                    <input type="radio" value="0" name="refund_total_voucher_off" id="refund_total_1" checked="checked" />
                                    {l s='Include amount of initial voucher: '}
                                </label>
                            </p>
                            <p class="radio">
                                <label id="lab_refund_total_2" for="refund_total_2">
                                    <input type="radio" value="1" name="refund_total_voucher_off" id="refund_total_2"/>
                                    {l s='Exclude amount of initial voucher: '}
                                </label>
                            </p>
                            <div class="nowrap radio-inline">
                                <label id="lab_refund_total_3" class="pull-left" for="refund_total_3">
                                    {l s='Amount of your choice: '}
                                    <input type="radio" value="2" name="refund_total_voucher_off" id="refund_total_3"/>
                                </label>
                                <div class="input-group col-lg-1 pull-left">
                                    <div class="input-group-addon">
                                        {$currency->prefix}
                                        {$currency->suffix}
                                    </div>
                                    <input type="text" class="input fixed-width-md" name="refund_total_voucher_choose" value="0"/>
                                </div>
                            </div>
                            {/if}
                        {/if}
                        </div>
                        {if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
                        <div class="row">
                            <input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="btn btn-default" />
                        </div>
                        {/if}
                    </div>
                    <div style="display:none;" class="partial_refund_fields">
                        <p class="checkbox">
                            <label for="reinjectQuantitiesRefund">
                                <input type="checkbox" id="reinjectQuantitiesRefund" name="reinjectQuantities" />
                                {l s='Re-stock products'}
                            </label>
                        </p>
                        <p class="checkbox">
                            <label for="generateDiscountRefund">
                                <input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" onclick="toggleShippingCost()" />
                                {l s='Generate a voucher'}
                            </label>
                        </p>
                        {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
                        <p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund:'}</p>
                        <p class="radio">
                            <label id="lab_refund_1" for="refund_1">
                                <input type="radio" value="0" name="refund_voucher_off" id="refund_1" checked="checked" />
                                {l s='Product(s) price: '}
                            </label>
                        </p>
                        <p class="radio">
                            <label id="lab_refund_2" for="refund_2">
                                <input type="radio" value="1" name="refund_voucher_off" id="refund_2"/>
                                {l s='Product(s) price, excluding amount of initial voucher: '}
                            </label>
                        </p>
                        <div class="nowrap radio-inline">
                                <label id="lab_refund_3" class="pull-left" for="refund_3">
                                    {l s='Amount of your choice: '}
                                    <input type="radio" value="2" name="refund_voucher_off" id="refund_3"/>
                                </label>
                                <div class="input-group col-lg-1 pull-left">
                                    <div class="input-group-addon">
                                        {$currency->prefix}
                                        {$currency->suffix}
                                    </div>
                                    <input type="text" class="input fixed-width-md" name="refund_voucher_choose" value="0"/>
                                </div>
                            </div>
                        {/if}
                        <br/>
                        <button type="submit" name="partialRefund" class="btn btn-default">
                            <i class="icon-check"></i> {l s='Partial refund'}
                        </button>
                    </div>
            </form>
        </div>
    </div>
