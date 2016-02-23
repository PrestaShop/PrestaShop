{extends file='page.tpl'}

{block name='page_title'}
    {l s='Order confirmation'}
{/block}

{block name='page_content_container' prepend}
    <section id="content-hook_payment_return">
        {$HOOK_PAYMENT_RETURN nofilter}
    </section>

    <section id="content-hook_order_confirmation">
        <h3>{l s='Your order is confirmed'}</h3>
        {if $url_to_invoice !== ''}
            <div>
                {l s='An email has been sent to your mail address'}{* user email address *}
                {l s='You can also'}<a href="{$url_to_invoice}">{l s='Download your invoice'}</a>
            </div>
        {/if}
        {$HOOK_ORDER_CONFIRMATION nofilter}
    </section>
{/block}

{block name='page_content_container'}
    <section id="content" class="page-content page-order-confirmation">
        <div id='order-items'>
            <h3>{l s='Order items'}</h3>
            <table>
                {foreach from=$order.products item=product}
                    <tr>
                        <td>
                    <span class="product-image media-middle">
                        <img class="" src="{$product.cover.medium.url}">
                    </span>
                        </td>
                        <td>
                            {$product.name}
                            {foreach from=$product.attributes key="attribute" item="value"}
                                - <span class="value">{$value}</span>
                            {/foreach}
                        </td>
                        <td>{$product.quantity}</td>
                        <td>{$product.price}</td>
                    </tr>
                {/foreach}
            </table>

            <hr>

            <table>
                <tr>
                    <td>{l s='Promo code'}</td>
                    <td>{$order.subtotals.discounts.amount}</td>
                </tr>
                <tr>
                    <td>{l s='Shipping cost'}</td>
                    <td>{$order.subtotals.shipping.amount}</td>
                </tr>
                {if isset($order.subtotals.tax) }
                    <tr>
                        <td>{l s='Taxes'}</td>
                        <td>{$order.subtotals.tax.amount}</td>
                    </tr>
                {/if}
                <tr>
                    <td>{l s='Total'}</td>
                    <td>{$order.total.amount}</td>
                </tr>
            </table>

        </div>
        <div id='order-details'>
            <h3>{l s='Order details'}</h3>
            <ul>
                <li>{l s='Order reference'} {$order.details.reference}</li>
                <li>{l s='Payment method'} {$order.details.payment}</li>
                <li>{l s='Shipping method'} {$order.carrier.name}</li>
            </ul>
        </div>

        {if $is_guest}
            <div id='registration-form'>
                <h4>{l s='Save time on your next order, sign up now'}</h4>
                {render file='customer/_partials/customer-form.tpl' ui=$register_form}
            </div>
        {/if}

    </section>
{/block}

{block name='page_content_container' append}
    <section id="content-hook-order-confirmation-footer">
        {$HOOK_ORDER_CONFIRMATION_FOOTER nofilter} {* StarterTheme: Create hook *}
    </section>
{/block}
