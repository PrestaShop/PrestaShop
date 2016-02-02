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
  <a href="?cancelAddress={$type}">{l s='Cancel'}</a>
  <button type="submit">{l s='Save Address'}</button>
{/block}
