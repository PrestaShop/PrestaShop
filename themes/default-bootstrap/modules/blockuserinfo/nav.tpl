<!-- Block user information module NAV  -->
{if $logged}
<div class="header_user_info">
	<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
</div>
{/if}
<div class="header_user_info">
	{if $logged}
		<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" rel="nofollow" title="{l s='Log me out'}">{l s='Sign out'}</a>
	{else}
		<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Login to your customer account'}">{l s='Sign in'}</a>
	{/if}
</div>
<!-- /Block usmodule NAV -->