<div class="user-info col-md-4">
  {if $logged}
	<a class="logout"  href="{$logout_url}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign out' mod='blockuserinfo'}</a>
    <a class="account" href="{$my_account_url}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow"><span>{$customerName}</span></a>
  {else}
	<a class="login" href="{$my_account_url}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">{l s='Sign in' mod='blockuserinfo'}</a>
  {/if}
</div>
