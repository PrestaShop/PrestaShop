<!-- Block myaccount module -->
	<div id="tmfooterlinks">
	<div>
    <h4><a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='blockmyaccountfooter'}</a></h4>
	
		<ul class="bullet">
			<li><a href="{$link->getPageLink('history', true)}" title="">{l s='My orders' mod='blockmyaccountfooter'}</a></li>
			{if $returnAllowed}<li><a href="{$link->getPageLink('order-follow', true)}" title="">{l s='My merchandise returns' mod='blockmyaccountfooter'}</a></li>{/if}
			<li><a href="{$link->getPageLink('order-slip', true)}" title="">{l s='My credit slips' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)}" title="">{l s='My addresses' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)}" title="">{l s='My personal info' mod='blockmyaccountfooter'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}" title="">{l s='My vouchers' mod='blockmyaccountfooter'}</a></li>{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
		</ul>

	</div>
</div>
<!-- /Block myaccount module -->