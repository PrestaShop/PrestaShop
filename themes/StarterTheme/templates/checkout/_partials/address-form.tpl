{extends "customer/_partials/address-form.tpl"}

{block "form_fields" append}
  <input type="hidden" name="saveAddress" value="{$type}">
  {if $type === "delivery"}
    <label>
      <input  name    = "use_same_address"
              type    = "checkbox"
              value   = "1"
              {if $use_same_address} checked {/if}
      >
      <span>{l s='Use this address for invoice too'}</span>
    </label>
  {/if}
{/block}

{block "form_buttons"}
  {if !$form_has_continue_button}
    <a href="?cancelAddress={$type}">{l s='Cancel'}</a>
    <button type="submit">{l s='Save Address'}</button>
  {else}
    {if $customer.addresses|count > 0}
      <a href="?cancelAddress={$type}">{l s='Cancel'}</a>
    {/if}
    <form>
      <button type="submit" class="continue" name="continue" value="1">
          {l s='Continue'}
      </button>
    </form>
  {/if}
{/block}
