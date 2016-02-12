{extends file='customer/_partials/address-form.tpl'}

{block name='form_field'}
  {if $field.name eq "alias"}
    {* we don't ask for alias here *}
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

{block name='form_fields' append}
  <input type="hidden" name="saveAddress" value="{$type}">
  {if $type === "delivery"}
    <div class="form-group row">
      <div class="col-md-3"></div>
      <div class="col-md-9">
        <input name = "use_same_address" type = "checkbox" value = "1" {if $use_same_address} checked {/if}>
        <label>{l s='Use this address for invoice too'}</label>
      </div>
    </div>
  {/if}
{/block}

{block name='form_buttons'}
  {if !$form_has_continue_button}
    <a href="?cancelAddress={$type}">{l s='Cancel'}</a>
    <button type="submit" class="form-control-submit">{l s='Save Address'}</button>
  {else}
    {if $customer.addresses|count > 0}
      <a href="?cancelAddress={$type}">{l s='Cancel'}</a>
    {/if}
    <form>
      <button type="submit" class="continue form-control-submit" name="confirm-addresses" value="1">
          {l s='Continue'}
      </button>
    </form>
  {/if}
{/block}
