                {if $customer->id}
                    <div class="panel-heading">
                        <i class="icon-user"></i>
                        {l s='Customer'}
                        <span class="badge">
                            <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
                                {if Configuration::get('PS_B2B_ENABLE')}{$customer->company} - {/if}
                                {$gender->name|escape:'html':'UTF-8'}
                                {$customer->firstname}
                                {$customer->lastname}
                            </a>
                        </span>
                        <span class="badge">
                            {l s='#'}{$customer->id}
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            {if ($customer->isGuest())}
                                {l s='This order has been placed by a guest.'}
                                {if (!Customer::customerExists($customer->email))}
                                    <form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;token={getAdminToken tab='AdminCustomers'}">
                                        <input type="hidden" name="id_lang" value="{$order->id_lang}" />
                                        <input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer'}" />
                                        <p class="help-block">{l s='This feature will generate a random password and send an email to the customer.'}</p>
                                    </form>
                                {else}
                                    <div class="alert alert-warning">
                                        {l s='A registered customer account has already claimed this email address'}
                                    </div>
                                {/if}
                            {else}
                                <dl class="well list-detail">
                                    <dt>{l s='Email'}</dt>
                                        <dd><a href="mailto:{$customer->email}"><i class="icon-envelope-o"></i> {$customer->email}</a></dd>
                                    <dt>{l s='Account registered'}</dt>
                                        <dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
                                    <dt>{l s='Valid orders placed'}</dt>
                                        <dd><span class="badge">{$customerStats['nb_orders']|intval}</span></dd>
                                    <dt>{l s='Total spent since registration'}</dt>
                                        <dd><span class="badge badge-success">{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</span></dd>
                                    {if Configuration::get('PS_B2B_ENABLE')}
                                        <dt>{l s='Siret'}</dt>
                                            <dd>{$customer->siret}</dd>
                                        <dt>{l s='APE'}</dt>
                                            <dd>{$customer->ape}</dd>
                                    {/if}
                                </dl>
                            {/if}
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group hidden-print">
                                <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="btn btn-default btn-block">{l s='View full details...'}</a>
                            </div>
                            <div class="panel panel-sm">
                                <div class="panel-heading">
                                    <i class="icon-eye-slash"></i>
                                    {l s='Private note'}
                                </div>
                                <form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id});return false;" >
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')">{$customer->note}</textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
                                                <i class="icon-save"></i>
                                                {l s='Save'}
                                            </button>
                                        </div>
                                    </div>
                                    <span id="note_feedback"></span>
                                </form>
                            </div>
                        </div>
                    </div>
                {/if}
                <!-- Tab nav -->
                <div class="row">
                    <ul class="nav nav-tabs" id="tabAddresses">
                        <li class="active">
                            <a href="#addressShipping">
                                <i class="icon-truck"></i>
                                {l s='Shipping address'}
                            </a>
                        </li>
                        <li>
                            <a href="#addressInvoice">
                                <i class="icon-file-text"></i>
                                {l s='Invoice address'}
                            </a>
                        </li>
                    </ul>
                    <!-- Tab content -->
                    <div class="tab-content panel">
                        <!-- Tab status -->
                        <div class="tab-pane  in active" id="addressShipping">
                            <!-- Addresses -->
                            <h4 class="visible-print">{l s='Shipping address'}</h4>
                            {if !$order->isVirtual()}
                            <!-- Shipping address -->
                                {if $can_edit}
                                    <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
                                        <div class="form-group">
                                            <div class="col-lg-9">
                                                <select name="id_address">
                                                    {foreach from=$customer_addresses item=address}
                                                    <option value="{$address['id_address']}"
                                                        {if $address['id_address'] == $order->id_address_delivery}
                                                            selected="selected"
                                                        {/if}>
                                                        {$address['alias']} -
                                                        {$address['address1']}
                                                        {$address['postcode']}
                                                        {$address['city']}
                                                        {if !empty($address['state'])}
                                                            {$address['state']}
                                                        {/if},
                                                        {$address['country']}
                                                    </option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change'}</button>
                                            </div>
                                        </div>
                                    </form>
                                {/if}
                                <div class="well">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
                                                <i class="icon-pencil"></i>
                                                {l s='Edit'}
                                            </a>
                                            {displayAddressDetail address=$addresses.delivery newLine='<br />'}
                                            {if $addresses.delivery->other}
                                                <hr />{$addresses.delivery->other}<br />
                                            {/if}
                                        </div>
                                        <div class="col-sm-6 hidden-print">
                                            <div id="map-delivery-canvas" style="height: 190px"></div>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                        <div class="tab-pane " id="addressInvoice">
                            <!-- Invoice address -->
                            <h4 class="visible-print">{l s='Invoice address'}</h4>
                            {if $can_edit}
                                <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
                                    <div class="form-group">
                                        <div class="col-lg-9">
                                            <select name="id_address">
                                                {foreach from=$customer_addresses item=address}
                                                <option value="{$address['id_address']}"
                                                    {if $address['id_address'] == $order->id_address_invoice}
                                                    selected="selected"
                                                    {/if}>
                                                    {$address['alias']} -
                                                    {$address['address1']}
                                                    {$address['postcode']}
                                                    {$address['city']}
                                                    {if !empty($address['state'])}
                                                        {$address['state']}
                                                    {/if},
                                                    {$address['country']}
                                                </option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change'}</button>
                                        </div>
                                    </div>
                                </form>
                            {/if}
                            <div class="well">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=2&amp;back={$smarty.server.REQUEST_URI|urlencode}&amp;token={getAdminToken tab='AdminAddresses'}">
                                            <i class="icon-pencil"></i>
                                            {l s='Edit'}
                                        </a>
                                        {displayAddressDetail address=$addresses.invoice newLine='<br />'}
                                        {if $addresses.invoice->other}
                                            <hr />{$addresses.invoice->other}<br />
                                        {/if}
                                    </div>
                                    <div class="col-sm-6 hidden-print">
                                        <div id="map-invoice-canvas" style="height: 190px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $('#tabAddresses a').click(function (e) {
                        e.preventDefault()
                        $(this).tab('show')
                    })
                </script>
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
                        });
                    </script>