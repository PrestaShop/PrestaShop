{extends "customer/_partials/customer-form.tpl"}

{block "form_field"}
  <div>
    {if $field.name === 'password' and $guest_allowed}
        <p class="create-account">{l s='Create an account and save time on your next order (optional)'}</p>
        {$smarty.block.parent}
    {else}
      {$smarty.block.parent}
    {/if}
  </div>
{/block}

{block "form_buttons"}
    <button type="submit" class="continue form-control-submit pull-xs-right" name="continue" value="1">
        {l s='Continue'}
    </button>
{/block}
