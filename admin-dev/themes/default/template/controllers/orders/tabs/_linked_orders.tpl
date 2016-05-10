            <!-- linked orders block -->
                <div class="panel-heading">
                    <i class="icon-cart"></i>
                    {l s='Linked orders'}
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    {l s='Order no. '}
                                </th>
                                <th>
                                    {l s='Status'}
                                </th>
                                <th>
                                    {l s='Amount'}
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $order->getBrother() as $brother_order}
                            <tr>
                                <td>
                                    <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">#{$brother_order->id}</a>
                                </td>
                                <td>
                                    {$brother_order->getCurrentOrderState()->name[$current_id_lang]}
                                </td>
                                <td>
                                    {displayPrice price=$brother_order->total_paid_tax_incl currency=$currency->id}
                                </td>
                                <td>
                                    <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                                        <i class="icon-eye-open"></i>
                                        {l s='See the order'}
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
