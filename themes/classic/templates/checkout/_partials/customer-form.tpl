{extends "customer/_partials/customer-form.tpl"}

{block "form_field"}
  {if $field.name === 'password' and $guest_allowed}
    <div>
      <p class="col-md-11 col-md-offset-1 _margin-top-medium">{l s='Choose a password to create an account and save time on your next order (optional)'}</p>
      {$smarty.block.parent}
    </div>
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

{block "form_buttons"}
    <button type="submit" class="continue _margin-top-medium submit-button center-block" name="continue" value="1">
        {l s='Continue'}
    </button>
{/block}
