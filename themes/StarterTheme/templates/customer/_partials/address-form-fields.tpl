{foreach from=$address_fields key=field_name item=data}

  {if $field_name == 'Country:name' || $field_name == 'country'}

    {block name="address_form_item_country"}
      <label>
        <span>{l s='Country'}</span>
        {if $data.required}
          {block name="required_field"}{include file="_partials/form-required-field.tpl"}{/block}
        {/if}
        {block name="form_item_country"}
          {include file="customer/_partials/form-item-country.tpl" countries=$countries sl_country=$address.id_country required=$data.required}
        {/block}
        {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$data.errors}{/block}
      </label>
    {/block}

  {elseif $field_name == 'State:name' && $address.id_state}

    {block name="address_form_item_state"}
      <label>
        <span>{l s='State'}</span>
        {if $data.required}
          {block name="required_field"}{include file="_partials/form-required-field.tpl"}{/block}
        {/if}
        {block name="form_item_state"}
          {include file="customer/_partials/form-item-state.tpl" states=$countries[$address.id][states] sl_state=$address.id_state required=$data.required}
        {/block}
        {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$data.errors}{/block}
      </label>
    {/block}

  {else}

    {block name="address_form_item_"|cat:$field_name}
      <label>
        <span>{$data.label}</span>
        {if $data.required}
          {block name="required_field"}{include file="_partials/form-required-field.tpl"}{/block}
        {/if}
        <input type="text" name="{$field_name}" id="{$field_name}" value="{$address.$field_name}" {if $data.required}required{/if}>
        {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$data.errors}{/block}
      </label>
    {/block}

  {/if}

{/foreach}

{if isset($address.id)}
  <input type="hidden" name="id_address" value="{$address.id}">
{/if}
