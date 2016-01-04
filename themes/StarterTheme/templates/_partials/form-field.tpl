{if $field.type === 'select'}
  <label>
    <span>{$field.label}</span>
    <select {if $field.required} required {/if} name="{$field.name}">
      {foreach from=$field.availableValues item="label" key="value"}
        <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
      {/foreach}
    </select>
  </label>
{else}
  <label>
    {if $field.type !== 'checkbox'}
      <span>{$field.label}</span>
    {/if}
    <input  {if $field.required} required {/if}
            name="{$field.name}"
            type="{$field.type}"
            value="{$field.value}"
    >
    {if $field.type === 'checkbox'}
      <span>{$field.label}</span>
    {/if}
  </label>
{/if}

{include file="_partials/form-field-errors.tpl" errors=$field.errors}
