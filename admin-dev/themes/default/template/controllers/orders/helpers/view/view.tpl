{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
    <script type="text/javascript">
    var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|addslashes}";
    var id_order = {$order->id};
    var id_lang = {$current_id_lang};
    var id_currency = {$order->id_currency};
    var id_customer = {$order->id_customer|intval};
    {assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
    var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
    var currency_sign = "{$currency->sign}";
    var currency_format = "{$currency->format}";
    var currency_blank = "{$currency->blank}";
    var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};
    var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
    var stock_management = {$stock_management|intval};
    var txt_add_product_stock_issue = "{l s='Are you sure you want to add this quantity?' js=1}";
    var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' js=1}";
    var txt_add_product_no_product = "{l s='Error: No product has been selected' js=1}";
    var txt_add_product_no_product_quantity = "{l s='Error: Quantity of products must be set' js=1}";
    var txt_add_product_no_product_price = "{l s='Error: Product price must be set' js=1}";
    var txt_confirm = "{l s='Are you sure?' js=1}";
    var statesShipped = new Array();
    var has_voucher = {if count($discounts)}1{else}0{/if};
    {foreach from=$states item=state}
        {if (isset($currentState->shipped) && !$currentState->shipped && $state['shipped'])}
            statesShipped.push({$state['id_order_state']});
        {/if}
    {/foreach}
    var order_discount_price = {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                                    {$order->total_discounts_tax_excl}
                                {else}
                                    {$order->total_discounts_tax_incl}
                                {/if};

    var errorRefund = "{l s='Error. You cannot refund a negative amount.'}";
    </script>

    {assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
    {if ($hook_invoice)}
        <div>{$hook_invoice}</div>
    {/if}

    <div class="panel kpi-container">
        <div class="row">
            <div class="col-xs-6 col-sm-3 box-stats color3" >
                <div class="kpi-content">
                    <i class="icon-calendar-empty"></i>
                    <span class="title">{l s='Date'}</span>
                    <span class="value">{dateFormat date=$order->date_add full=false}</span>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3 box-stats color4" >
                <div class="kpi-content">
                    <i class="icon-money"></i>
                    <span class="title">{l s='Total'}</span>
                    <span class="value">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3 box-stats color2" >
                <div class="kpi-content">
                    <i class="icon-comments"></i>
                    <span class="title">{l s='Messages'}</span>
                    <span class="value"><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">{sizeof($customer_thread_message)}</a></span>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3 box-stats color1" id="kpiProducts">
                <a href="#products">
                    <div class="kpi-content">
                        <i class="icon-book"></i>
                        <span class="title">{l s='Products'}</span>
                        <span class="value">{sizeof($products)}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="panel">
            <div class="row">
                <div class="col-xs-6">
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
                <div class="col-xs-6">
                    <div class="well" id="status">
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
                    </div>
                </div>
            </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <i class="icon-credit-card"></i>
            {l s='Order'}
            <span class="badge">{$order->reference}</span>
            <span class="badge">{l s="#"}{$order->id}</span>
            <div class="panel-heading-action">
                <div class="btn-group">
                    <a class="btn btn-default{if !$previousOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$previousOrder|intval}">
                        <i class="icon-backward"></i>
                    </a>
                    <a class="btn btn-default{if !$nextOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$nextOrder|intval}">
                        <i class="icon-forward"></i>
                    </a>
                </div>
            </div>
        </div>
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

        {assign var="display_admin_order" value={hook h="displayAdminOrder" id_order=$order->id}}

        <ul id="tabViews" class="nav nav-tabs">
            <li class="active">
                <a href="#order_tab_products" id="order_tab_products_link">
                     <i class="icon-shopping-cart"></i>
                    {l s='Products'}
                </a>
            </li>
            <li>
                <a href="#order_tab_customer" id="order_tab_customer_link">
                    <i class="icon-user"></i>
                    {l s='Customer'}
                </a>
            </li>
            <li>
                <a href="#order_tab_messages" id="order_tab_messages_link">
                <i class="icon-envelope"></i>
                    {l s='Messages'}
                </a>
            </li>
            <li>
                <a href="#order_tab_order_info" id="order_tab_order_info_link">
                    <i class="icon-credit-card"></i>
                    {l s='Order Information'}
                </a>
            </li>
            <li>
                <a href="#order_tab_payments" id="order_tab_payments_link">
                    <i class="icon-money"></i>
                    {l s='Payments'}
                </a>
            </li>
            {if count($order->getBrother()) > 0}
            <li>
                <a href="#order_tab_linked_orders" id="order_tab_linked_orders_link">
                    <i class="icon-cart"></i>
                    {l s='Linked Orders'}
                </a>
            </li>
            {/if}
            {if $display_admin_order}
                <li>
                    <a href="#order_tab_display_admin_order" id="order_tab_display_admin_order_link">
                        <i class="icon-anchor"></i>
                        {l s='Extra'}
                    </a>
                </li>
            {/if}
        </ul>
        <div class="tab-content panel">
            <div class="tab-pane active" id="order_tab_products">
                {include file='controllers/orders/tabs/_products.tpl'}
            </div>
            <div class="tab-pane" id="order_tab_customer">
                {include file='controllers/orders/tabs/_customer.tpl'}
            </div>
            <div class="tab-pane" id="order_tab_messages">
                {include file='controllers/orders/tabs/_messages.tpl'}
            </div>
            <div class="tab-pane" id="order_tab_order_info">
                {include file='controllers/orders/tabs/_order_information.tpl'}
            </div>
            <div class="tab-pane" id="order_tab_payments">
                {include file='controllers/orders/tabs/_payments.tpl'}
            </div>
            {if count($order->getBrother()) > 0}
            <div class="tab-pane" id="order_tab_linked_orders">
                {include file='controllers/orders/tabs/_linked_orders.tpl'}
            </div>
            {/if}
            {if $display_admin_order}
            <div class="tab-pane" id="order_tab_display_admin_order">
                {$display_admin_order}
            </div>
            {/if}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            {hook h="displayAdminOrderLeft" id_order=$order->id}
        </div>
        <div class="col-lg-5">
            {hook h="displayAdminOrderRight" id_order=$order->id}
        </div>
    </div>

    <script type="text/javascript">
        $('#tabViews a').click(function (e) {
            e.preventDefault()
            $(this).tab('show');
            if ($(this).attr('id') == 'order_tab_messages_link') {
                $('#order_message_chosen').attr('style', 'width: 90%');
            }
        });

        $('#kpiProducts a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            $('#tabViews').each(function() {
                $(this).find('li').each(function() {
                    $(this).removeClass('active');
                });
            });
            $("#tabViews li").first().addClass("active");
            $.scrollTo($("#start_products"), 50);
        });
    </script>

    <script type="text/javascript">
        $('.datepicker').datetimepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd',
            // Define a custom regional settings in order to use PrestaShop translation tools
            currentText: '{l s='Now' js=1}',
            closeText: '{l s='Done' js=1}',
            ampm: false,
            amNames: ['AM', 'A'],
            pmNames: ['PM', 'P'],
            timeFormat: 'hh:mm:ss tt',
            timeSuffix: '',
            timeOnlyTitle: '{l s='Choose Time' js=1}',
            timeText: '{l s='Time' js=1}',
            hourText: '{l s='Hour' js=1}',
            minuteText: '{l s='Minute' js=1}'
        });
    </script>

{/block}
