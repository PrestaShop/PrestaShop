{extends "customer/_partials/customer-form.tpl"}

{block "form_field"}
  <div>
    {if $field.name === 'password' and $guest_allowed}
        <p>
          <span class="font-weight-bold">{l s='Create an account'}</span> <span class="font-italic">{l s='(optional)'}</span>
          <br>
          <span class="text-light">{l s='And save time on your next order!'}</span>
        </p>
        {$smarty.block.parent}
    {else}
      {$smarty.block.parent}
    {/if}
  </div>
{/block}

{block "form_buttons"}
    <button type="submit" class="continue btn btn-primary pull-xs-right" name="continue" value="1">
        {l s='Continue'}
    </button>
{/block}
