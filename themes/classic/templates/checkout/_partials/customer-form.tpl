{extends "customer/_partials/customer-form.tpl"}

{block "form_field"}
  {if $field.name === 'password' and $guest_allowed}
    <div>
      <p>{l s='Choose a password to create an account and save time on your next order (optional)'}</p>
      {$smarty.block.parent}
    </div>
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

{block "form_buttons"}
    <button type="submit" class="continue" name="continue" value="1">
        {l s='Continue'}
    </button>
{/block}
