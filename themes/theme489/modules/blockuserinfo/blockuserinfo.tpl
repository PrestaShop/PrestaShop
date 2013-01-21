<!-- Block user information module HEADER -->
<ul id="header_user">
	{if !$PS_CATALOG_MODE}
	<li id="shopping_cart">
		<a href="{$link->getPageLink("$order_process", true)}" title="{l s='Your Shopping Cart' mod='blockuserinfo'}">{l s='Cart:' mod='blockuserinfo'}
		<span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
		<span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='blockuserinfo'}</span>
		<span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='blockuserinfo'}</span>
		{if $cart_qties >= 0}
			<span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">
				{if $priceDisplay == 1}
					{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
					{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
				{else}
					{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
					{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
				{/if}
			</span>
		{/if}
		<span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span>
		</a>
	</li>
	{/if}
	<li id="user_info">
		{l s='Welcome' mod='blockuserinfo'}
		{if $logged}
			<a class="account" href="{$link->getPageLink('my-account', true)}">{$cookie->customer_firstname} {$cookie->customer_lastname}</a>
			<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}">(&nbsp;{l s='Log out' mod='blockuserinfo'}&nbsp;)</a>
		{else}
			<a class="login" href="{$link->getPageLink('my-account', true)}">(&nbsp;{l s='Log in' mod='blockuserinfo'}&nbsp;)</a>
		{/if}
	</li>
</ul>
<!-- /Block user information module HEADER -->