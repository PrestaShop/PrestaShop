                <!-- Tab nav -->
                <ul class="nav nav-tabs" id="tabOrder">
                    {$HOOK_TAB_ORDER}
                    <li class="active">
                        <a href="#documents">
                            <i class="icon-file-text"></i>
                            {l s='Documents'} <span class="badge">{$order->getDocuments()|@count}</span>
                        </a>
                    </li>
                </ul>
                <!-- Tab content -->
                <div class="tab-content panel">
                    {$HOOK_CONTENT_ORDER}
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