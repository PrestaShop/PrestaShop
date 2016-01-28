<div class="user-info col-md-2 _margin-top-small pull-xs-right">
  <i class="material-icons _gray-darker">&#xE7FF;</i>
  {if $logged}
	<a class="logout"  href="{$logout_url}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign out' mod='blockuserinfo'}</a>
    <a class="account" href="{$my_account_url}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow"><span>{$customerName}</span></a>
  {else}
	<a class="login" href="{$my_account_url}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">{l s='Sign In' mod='blockuserinfo'}</a>
  {/if}
</div>
