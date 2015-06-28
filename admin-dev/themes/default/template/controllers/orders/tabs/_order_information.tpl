                <!-- Orders Actions -->
                <div class="well hidden-print">
                    <a class="btn btn-default" href="javascript:window.print()">
                        <i class="icon-print"></i>
                        {l s='Print order'}
                    </a>
                    &nbsp;
                    {if Configuration::get('PS_INVOICE') && count($invoices_collection) && $order->invoice_number}
                        <a data-selenium-id="view_invoice" class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id|intval}">
                            <i class="icon-file"></i>
                            {l s='View invoice'}
                        </a>
                    {else}
                        <span class="span label label-inactive">
                            <i class="icon-remove"></i>
                            {l s='No invoice'}
                        </span>
                    {/if}
                    &nbsp;
                    {if $order->delivery_number}
                        <a class="btn btn-default _blank"  href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id|intval}">
                            <i class="icon-truck"></i>
                            {l s='View delivery slip'}
                        </a>
                    {else}
                        <span class="span label label-inactive">
                            <i class="icon-remove"></i>
                            {l s='No delivery slip'}
                        </span>
                    {/if}
                    &nbsp;
                    {if Configuration::get('PS_ORDER_RETURN')}
                        <a id="desc-order-standard_refund" class="btn btn-default" href="#refundForm">
                            <i class="icon-exchange"></i>
                            {if $order->hasBeenShipped()}
                                {l s='Return products'}
                            {elseif $order->hasBeenPaid()}
                                {l s='Standard refund'}
                            {else}
                                {l s='Cancel products'}
                            {/if}
                        </a>
                        &nbsp;
                    {/if}
                    {if $order->hasInvoice()}
                        <a id="desc-order-partial_refund" class="btn btn-default" href="#refundForm">
                            <i class="icon-exchange"></i>
                            {l s='Partial refund'}
                        </a>
                    {/if}
                </div>
                <!-- Tab nav -->
                <ul class="nav nav-tabs" id="tabOrder">
                    {$HOOK_TAB_ORDER}
                    <li class="active">
                        <a href="#status">
                            <i class="icon-time"></i>
                            {l s='Status'} <span class="badge">{$history|@count}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#documents">
                            <i class="icon-file-text"></i>
                            {l s='Documents'} <span class="badge">{$order->getDocuments()|@count}</span>
                        </a>
                    </li>
                </ul>
                <!-- Tab content -->
                <div class="tab-content panel">
                    {$HOOK_CONTENT_ORDER}
                    <!-- Tab status -->
                    <div class="tab-pane active" id="status">
                        <h4 class="visible-print">{l s='Status'} <span class="badge">({$history|@count})</span></h4>
                        <!-- History of status -->
                        <div class="table-responsive">
                            <table class="table history-status row-margin-bottom">
                                <tbody>
                                    {foreach from=$history item=row key=key}
                                        {if ($key == 0)}
                                            <tr>
                                                <td style="background-color:{$row['color']}"><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td>
                                                <td style="background-color:{$row['color']};color:{$row['text-color']}">{$row['ostate_name']|stripslashes}</td>
                                                <td style="background-color:{$row['color']};color:{$row['text-color']}">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
                                                <td style="background-color:{$row['color']};color:{$row['text-color']}">{dateFormat date=$row['date_add'] full=true}</td>
                                                <td style="background-color:{$row['color']};color:{$row['text-color']}" class="text-right">
                                                    {if $row['send_email']|intval}
                                                        <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer'}">
                                                            <i class="icon-mail-reply"></i>
                                                            {l s='Resend email'}
                                                        </a>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {else}
                                            <tr>
                                                <td><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" /></td>
                                                <td>{$row['ostate_name']|stripslashes}</td>
                                                <td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{else}&nbsp;{/if}</td>
                                                <td>{dateFormat date=$row['date_add'] full=true}</td>
                                                <td class="text-right">
                                                    {if $row['send_email']|intval}
                                                        <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer'}">
                                                            <i class="icon-mail-reply"></i>
                                                            {l s='Resend email'}
                                                        </a>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <!-- Change status form -->
                        <form action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;token={$smarty.get.token}" method="post" class="form-horizontal well hidden-print">
                            <div class="row">
                                <div class="col-lg-9">
                                    <select id="id_order_state" class="chosen form-control" name="id_order_state">
                                    {foreach from=$states item=state}
                                        <option value="{$state['id_order_state']|intval}"{if isset($currentState) && $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape}</option>
                                    {/foreach}
                                    </select>
                                    <input type="hidden" name="id_order" value="{$order->id}" />
                                </div>
                                <div class="col-lg-3">
                                    <button type="submit" name="submitState" class="btn btn-primary">
                                        {l s='Update status'}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Tab documents -->
                    <div class="tab-pane" id="documents">
                        <h4 class="visible-print">{l s='Documents'} <span class="badge">({$order->getDocuments()|@count})</span></h4>
                        {* Include document template *}
                        {include file='controllers/orders/_documents.tpl'}
                    </div>
                </div>
                <script>
                    $('#tabOrder a').click(function (e) {
                        e.preventDefault()
                        $(this).tab('show')
                    })
                </script>
                <hr />
                <!-- Tab nav -->
                <ul class="nav nav-tabs" id="myTab">
                    {$HOOK_TAB_SHIP}
                    <li class="active">
                        <a href="#shipping">
                            <i class="icon-truck "></i>
                            {l s='Shipping'} <span class="badge">{$order->getShipping()|@count}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#returns">
                            <i class="icon-undo"></i>
                            {l s='Merchandise Returns'} <span class="badge">{$order->getReturn()|@count}</span>
                        </a>
                    </li>
                </ul>
                <!-- Tab content -->
                <div class="tab-content panel">
                {$HOOK_CONTENT_SHIP}
                    <!-- Tab shipping -->
                    <div class="tab-pane active" id="shipping">
                        <h4 class="visible-print">{l s='Shipping'} <span class="badge">({$order->getShipping()|@count})</span></h4>
                        <!-- Shipping block -->
                        {if !$order->isVirtual()}
                        <div class="form-horizontal">
                            {if $order->gift_message}
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Message'}</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">{$order->gift_message|nl2br}</p>
                                </div>
                            </div>
                            {/if}
                            {include file='controllers/orders/_shipping.tpl'}
                            {if $carrierModuleCall}
                                {$carrierModuleCall}
                            {/if}
                            <hr />
                            {if $order->recyclable}
                                <span class="label label-success"><i class="icon-check"></i> {l s='Recycled packaging'}</span>
                            {else}
                                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Recycled packaging'}</span>
                            {/if}

                            {if $order->gift}
                                <span class="label label-success"><i class="icon-check"></i> {l s='Gift wrapping'}</span>
                            {else}
                                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Gift wrapping'}</span>
                            {/if}
                        </div>
                        {/if}
                    </div>
                    <!-- Tab returns -->
                    <div class="tab-pane" id="returns">
                        <h4 class="visible-print">{l s='Merchandise Returns'} <span class="badge">({$order->getReturn()|@count})</span></h4>
                        {if !$order->isVirtual()}
                        <!-- Return block -->
                            {if $order->getReturn()|count > 0}
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box ">Date</span></th>
                                            <th><span class="title_box ">Type</span></th>
                                            <th><span class="title_box ">Carrier</span></th>
                                            <th><span class="title_box ">Tracking number</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$order->getReturn() item=line}
                                        <tr>
                                            <td>{$line.date_add}</td>
                                            <td>{$line.type}</td>
                                            <td>{$line.state_name}</td>
                                            <td class="actions">
                                                <span class="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number}</a>{elseif isset($line.tracking_number)}{$line.tracking_number}{/if}</span>
                                                {if $line.can_edit}
                                                <form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|intval}{else}0{/if}&amp;id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
                                                    <span class="shipping_number_edit" style="display:none;">
                                                        <button type="button" name="tracking_number">
                                                            {$line.tracking_number|htmlentities}
                                                        </button>
                                                        <button type="submit" class="btn btn-default" name="submitShippingNumber">
                                                            {l s='Update'}
                                                        </button>
                                                    </span>
                                                    <button href="#" class="edit_shipping_number_link">
                                                        <i class="icon-pencil"></i>
                                                        {l s='Edit'}
                                                    </button>
                                                    <button href="#" class="cancel_shipping_number_link" style="display: none;">
                                                        <i class="icon-remove"></i>
                                                        {l s='Cancel'}
                                                    </button>
                                                </form>
                                                {/if}
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                            {else}
                            <div class="list-empty hidden-print">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No merchandise returned yet'}
                                </div>
                            </div>
                            {/if}
                            {if $carrierModuleCall}
                                {$carrierModuleCall}
                            {/if}
                        {/if}
                    </div>
                </div>
                <script>
                    $('#myTab a').click(function (e) {
                        e.preventDefault()
                        $(this).tab('show')
                    })
                </script>
            <!-- Payments block -->
            <div id="formAddPaymentPanel" class="panel">
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
            <!-- Sources block -->
            {if (sizeof($sources))}
                <div class="panel-heading">
                    <i class="icon-globe"></i>
                    {l s='Sources'} <span class="badge">{$sources|@count}</span>
                </div>
                <ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
                {foreach from=$sources item=source}
                    <li>
                        {dateFormat date=$source['date_add'] full=true}<br />
                        <b>{l s='From'}</b>{if $source['http_referer'] != ''}<a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
                        <b>{l s='To'}</b> <a href="http://{$source['request_uri']}">{$source['request_uri']|truncate:100:'...'}</a><br />
                        {if $source['keywords']}<b>{l s='Keywords'}</b> {$source['keywords']}<br />{/if}<br />
                    </li>
                {/foreach}
                </ul>
            {/if}

    <script type="text/javascript">
        var geocoder = new google.maps.Geocoder();
        var delivery_map, invoice_map;

        $(document).ready(function()
        {
            $(".textarea-autosize").autosize();

            geocoder.geocode({
                address: '{$addresses.delivery->address1|@addcslashes:'\''},{$addresses.delivery->postcode|@addcslashes:'\''},{$addresses.delivery->city|@addcslashes:'\''}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|@addcslashes:'\''}{/if},{$addresses.delivery->country|@addcslashes:'\''}'
                }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK)
                {
                    delivery_map = new google.maps.Map(document.getElementById('map-delivery-canvas'), {
                        zoom: 10,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: results[0].geometry.location
                    });
                    var delivery_marker = new google.maps.Marker({
                        map: delivery_map,
                        position: results[0].geometry.location,
                        url: 'http://maps.google.com?q={$addresses.delivery->address1|urlencode},{$addresses.delivery->postcode|urlencode},{$addresses.delivery->city|urlencode}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|urlencode}{/if},{$addresses.delivery->country|urlencode}'
                    });
                    google.maps.event.addListener(delivery_marker, 'click', function() {
                        window.open(delivery_marker.url);
                    });
                }
            });

            geocoder.geocode({
                address: '{$addresses.invoice->address1|@addcslashes:'\''},{$addresses.invoice->postcode|@addcslashes:'\''},{$addresses.invoice->city|@addcslashes:'\''}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|@addcslashes:'\''}{/if},{$addresses.invoice->country|@addcslashes:'\''}'
                }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK)
                {
                    invoice_map = new google.maps.Map(document.getElementById('map-invoice-canvas'), {
                        zoom: 10,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: results[0].geometry.location
                    });
                    invoice_marker = new google.maps.Marker({
                        map: invoice_map,
                        position: results[0].geometry.location,
                        url: 'http://maps.google.com?q={$addresses.invoice->address1|urlencode},{$addresses.invoice->postcode|urlencode},{$addresses.invoice->city|urlencode}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|urlencode}{/if},{$addresses.invoice->country|urlencode}'
                    });
                    google.maps.event.addListener(invoice_marker, 'click', function() {
                        window.open(invoice_marker.url);
                    });
                }
            });

            var date = new Date();
            var hours = date.getHours();
            if (hours < 10)
                hours = "0" + hours;
            var mins = date.getMinutes();
            if (mins < 10)
                mins = "0" + mins;
            var secs = date.getSeconds();
            if (secs < 10)
                secs = "0" + secs;

            $('.datepicker').datetimepicker({
                prevText: '',
                nextText: '',
                dateFormat: 'yy-mm-dd ' + hours + ':' + mins + ':' + secs
            });
        });

        // Fix wrong maps center when map is hidden
        $('#tabAddresses').click(function(){
            x = delivery_map.getZoom();
            c = delivery_map.getCenter();
            google.maps.event.trigger(delivery_map, 'resize');
            delivery_map.setZoom(x);
            delivery_map.setCenter(c);

            x = invoice_map.getZoom();
            c = invoice_map.getCenter();
            google.maps.event.trigger(invoice_map, 'resize');
            invoice_map.setZoom(x);
            invoice_map.setCenter(c);
        });
    </script>