{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='Order confirmation'}</h1>
{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}
{$HOOK_ORDER_CONFIRMATION}
{$HOOK_PAYMENT_RETURN}
<br />
{if $is_guest}
	<p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent to you via e-mail.'}</p>
	<ul class="footer_links">
		<li>
			<a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")}" title="{l s='Follow my order'}"><img src="{$img_dir}icon/order.png" alt="{l s='Follow my order'}" class="icon" /></a>
			<a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")}" title="{l s='Follow my order'}">{l s='Follow my order'}</a>
		</li>
	</ul>
{else}
<ul class="footer_links">
	<li>
		<a href="{$link->getPageLink('history', true)}" title="{l s='Back to orders'}"><img src="{$img_dir}icon/order.png" alt="{l s='Back to orders'}" class="icon" /></a>
		<a href="{$link->getPageLink('history', true)}" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
	</li>
</ul>
{/if}