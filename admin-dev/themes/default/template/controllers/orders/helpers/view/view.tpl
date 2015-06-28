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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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
            <div class="col-xs-6 col-sm-3 box-stats color1" >
                <a href="#start_products">
                    <div class="kpi-content">
                        <i class="icon-book"></i>
                        <span class="title">{l s='Products'}</span>
                        <span class="value">{sizeof($products)}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
    {if ($hook_invoice)}
    <div>{$hook_invoice}</div>
    {/if}
            {hook h="displayAdminOrderLeft" id_order=$order->id}
            {hook h="displayAdminOrderRight" id_order=$order->id}
            {hook h="displayAdminOrder" id_order=$order->id}
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
    <ul id="tabViews" class="nav nav-tabs">
        <li class="active">
            <a href="#products">
                {l s='Products'}
            </a>
        </li>
        <li>
            <a href="#customer">
                {l s='Customer'}
            </a>
        </li>
        <li>
            <a href="#messages">
                {l s='Messages'}
            </a>
        </li>
        <li>
            <a href="#order_info">
                {l s='Order Information'}
            </a>
        </li>
        <li>
            <a href="#linked_orders">
                {l s='Linked Orders '}
            </a>
        </li>
    </ul>
        <div class="tab-content panel">
            <div class="tab-pane" id="products">
                {include file='controllers/orders/tabs/_products.tpl'}
            </div>
            <div class="tab-pane" id="customer">
                {include file='controllers/orders/tabs/_customer.tpl'}
            </div>
            <div class="tab-pane" id="messages">
                {include file='controllers/orders/tabs/_messages.tpl'}
            </div>
            <div class="tab-pane" id="order_info">
                {include file='controllers/orders/tabs/_order_information.tpl'}
            </div>
            <div class="tab-pane" id="linked_orders">
            {include file='controllers/orders/tabs/_linked_orders.tpl'}
            </div>
        </div>
    </div>
                <script>
                    $('#tabViews a').click(function (e) {
                        e.preventDefault()
                        $(this).tab('show')
                    })
                </script>


    <script type="text/javascript">
        $(document).ready(function()
        {
            $(".textarea-autosize").autosize();

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

    </script>
{/block}
