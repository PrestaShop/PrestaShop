<!-- Payments block -->
            <div id="formAddPaymentPanel" class="">
                <div class="panel-heading">
                    <i class="icon-money"></i>
                    {l s="Payment"} <span class="badge">{$order->getOrderPayments()|@count}</span>
                </div>
                {if count($order->getOrderPayments()) > 0}
                    <p class="alert alert-danger"{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || (isset($currentState) && $currentState->id == 6)} style="display: none;"{/if}>
                        {l s='Warning'}
                        <strong>{displayPrice price=$total_paid currency=$currency->id}</strong>
                        {l s='paid instead of'}
                        <strong class="total_paid">{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</strong>
                        {foreach $order->getBrother() as $brother_order}
                            {if $brother_order@first}
                                {if count($order->getBrother()) == 1}
                                    <br />{l s='This warning also concerns order '}
                                {else}
                                    <br />{l s='This warning also concerns the next orders:'}
                                {/if}
                            {/if}
                            <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                                #{'%06d'|sprintf:$brother_order->id}
                            </a>
                        {/foreach}
                    </p>
                {/if}
                <form id="formAddPayment"  method="post" action="{$current_index}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><span class="title_box ">{l s='Date'}</span></th>
                                    <th><span class="title_box ">{l s='Payment method'}</span></th>
                                    <th><span class="title_box ">{l s='Transaction ID'}</span></th>
                                    <th><span class="title_box ">{l s='Amount'}</span></th>
                                    <th><span class="title_box ">{l s='Invoice'}</span></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$order->getOrderPaymentCollection() item=payment}
                                <tr>
                                    <td>{dateFormat date=$payment->date_add full=true}</td>
                                    <td>{$payment->payment_method|escape:'html':'UTF-8'}</td>
                                    <td>{$payment->transaction_id|escape:'html':'UTF-8'}</td>
                                    <td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
                                    <td>
                                    {if $invoice = $payment->getOrderInvoice($order->id)}
                                        {$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}
                                    {else}
                                    {/if}
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-default open_payment_information">
                                            <i class="icon-search"></i>
                                            {l s='Details'}
                                        </button>
                                    </td>
                                </tr>
                                <tr class="payment_information" style="display: none;">
                                    <td colspan="5">
                                        <p>
                                            <b>{l s='Card Number'}</b>&nbsp;
                                            {if $payment->card_number}
                                                {$payment->card_number}
                                            {else}
                                                <i>{l s='Not defined'}</i>
                                            {/if}
                                        </p>
                                        <p>
                                            <b>{l s='Card Brand'}</b>&nbsp;
                                            {if $payment->card_brand}
                                                {$payment->card_brand}
                                            {else}
                                                <i>{l s='Not defined'}</i>
                                            {/if}
                                        </p>
                                        <p>
                                            <b>{l s='Card Expiration'}</b>&nbsp;
                                            {if $payment->card_expiration}
                                                {$payment->card_expiration}
                                            {else}
                                                <i>{l s='Not defined'}</i>
                                            {/if}
                                        </p>
                                        <p>
                                            <b>{l s='Card Holder'}</b>&nbsp;
                                            {if $payment->card_holder}
                                                {$payment->card_holder}
                                            {else}
                                                <i>{l s='Not defined'}</i>
                                            {/if}
                                        </p>
                                    </td>
                                </tr>
                                {foreachelse}
                                <tr>
                                    <td class="list-empty hidden-print" colspan="6">
                                        <div class="list-empty-msg">
                                            <i class="icon-warning-sign list-empty-icon"></i>
                                            {l s='No payment methods are available'}
                                        </div>
                                    </td>
                                </tr>
                                {/foreach}
                                <tr class="current-edit hidden-print">
                                    <td>
                                        <div class="input-group fixed-width-xl">
                                            <input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')}" />
                                            <div class="input-group-addon">
                                                <i class="icon-calendar-o"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input name="payment_method" list="payment_method" class="payment_method">
                                        <datalist id="payment_method">
                                        {foreach from=$payment_methods item=payment_method}
                                            <option value="{$payment_method}">
                                        {/foreach}
                                        </datalist>
                                    </td>
                                    <td>
                                        <input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
                                    </td>
                                    <td>
                                        <input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
                                        <select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
                                            {foreach from=$currencies item=current_currency}
                                                <option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td>
                                        {if count($invoices_collection) > 0}
                                            <select name="payment_invoice" id="payment_invoice">
                                            {foreach from=$invoices_collection item=invoice}
                                                <option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}</option>
                                            {/foreach}
                                            </select>
                                        {/if}
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-primary" type="submit" name="submitAddPayment">
                                            {l s='Add'}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
                {if (!$order->valid && sizeof($currencies) > 1)}
                    <form class="form-horizontal well" method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                        <div class="row">
                            <label class="control-label col-lg-3">{l s='Change currency'}</label>
                            <div class="col-lg-6">
                                <select name="new_currency">
                                {foreach from=$currencies item=currency_change}
                                    {if $currency_change['id_currency'] != $order->id_currency}
                                    <option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
                                    {/if}
                                {/foreach}
                                </select>
                                <p class="help-block">{l s='Do not forget to update your exchange rate before making this change.'}</p>
                            </div>
                            <div class="col-lg-3">
                                <button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> {l s='Change'}</button>
                            </div>
                        </div>
                    </form>
                {/if}
            </div>