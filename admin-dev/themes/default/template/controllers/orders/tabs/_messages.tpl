                <div class="panel-heading">
                    <i class="icon-envelope"></i> {l s='Messages'} <span class="badge">{sizeof($customer_thread_message)}</span>
                </div>
                {if (sizeof($messages))}
                    <div class="panel panel-highlighted">
                        <div class="message-item">
                            {foreach from=$messages item=message}
                                <div class="message-avatar">
                                    <div class="avatar-md">
                                        <i class="icon-user icon-2x"></i>
                                    </div>
                                </div>
                                <div class="message-body">

                                    <span class="message-date">&nbsp;<i class="icon-calendar"></i>
                                        {dateFormat date=$message['date_add']} -
                                    </span>
                                    <h4 class="message-item-heading">
                                        {if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
                                            {$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
                                        {/if}
                                        {if ($message['private'] == 1)}
                                            <span class="badge badge-info">{l s='Private'}</span>
                                        {/if}
                                    </h4>
                                    <p class="message-item-text">
                                        {$message['message']|escape:'html':'UTF-8'|nl2br}
                                    </p>
                                </div>
                                {*if ($message['is_new_for_me'])}
                                    <a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.server.REQUEST_URI}&amp;token={$smarty.get.token}&amp;messageReaded={$message['id_message']}">
                                        <i class="icon-ok"></i>
                                    </a>
                                {/if*}
                            {/foreach}
                        </div>
                    </div>
                {/if}
                <div id="messages" class="well hidden-print">
                    <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?'}');">
                        <div id="message" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Choose a standard message'}</label>
                                <div class="col-lg-9">
                                    <select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
                                        <option value="0" selected="selected">-</option>
                                        {foreach from=$orderMessages item=orderMessage}
                                        <option value="{$orderMessage['message']|escape:'html':'UTF-8'}">{$orderMessage['name']}</option>
                                        {/foreach}
                                    </select>
                                    <p class="help-block">
                                        <a href="{$link->getAdminLink('AdminOrderMessage')|escape:'html':'UTF-8'}">
                                            {l s='Configure predefined messages'}
                                            <i class="icon-external-link"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Display to customer?'}</label>
                                <div class="col-lg-9">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="visibility" id="visibility_on" value="0" />
                                        <label for="visibility_on">
                                            {l s='Yes'}
                                        </label>
                                        <input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" />
                                        <label for="visibility_off">
                                            {l s='No'}
                                        </label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Message'}</label>
                                <div class="col-lg-9">
                                    <textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
                                    <p id="nbchars"></p>
                                </div>
                            </div>

                            <input type="hidden" name="id_order" value="{$order->id}" />
                            <input type="hidden" name="id_customer" value="{$order->id_customer}" />
                            <button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
                                {l s='Send message'}
                            </button>
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
                                {l s='Show all messages'}
                                <i class="icon-external-link"></i>
                            </a>
                        </div>
                    </form>
                </div>