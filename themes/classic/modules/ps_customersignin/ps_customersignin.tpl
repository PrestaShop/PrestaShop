<div class="user-info">
  <i class="material-icons">&#xE7FF;</i>
  {if $logged}
	  <a class="logout" href="{$logout_url}" rel="nofollow">{l s='Sign out' d='Shop.Theme.Actions'}</a>
    <a class="account" href="{$my_account_url}" title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}" rel="nofollow">
      <span>{$customerName}</span>
    </a>
  {else}
    <a class="login" href="{$my_account_url}" rel="nofollow" title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}">
      {l s='Sign in' d='Shop.Theme.Actions'}
    </a>
  {/if}
</div>
