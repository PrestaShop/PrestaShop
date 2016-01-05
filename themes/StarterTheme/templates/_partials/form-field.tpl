{if $field.type === 'select'}
  <label>
    <span>{$field.label}</span>
    <select {if $field.required} required {/if} name="{$field.name}">
      {foreach from=$field.availableValues item="label" key="value"}
        <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
      {/foreach}
    </select>
  </label>
{elseif $field.type === 'checkbox'}
  <label>
    <input  {if $field.required} required {/if}
            name="{$field.name}"
            type="{$field.type}"
            value="1"
            {if $field.value} checked {/if}
    >
    <span>{$field.label}</span>
  </label>
{else}
  <label>
    <span>{$field.label}</span>
    <input  {if $field.required} required {/if}
            name="{$field.name}"
            type="{$field.type}"
            value="{$field.value}"
    >
  </label>
{/if}

{include file="_partials/form-field-errors.tpl" errors=$field.errors}
