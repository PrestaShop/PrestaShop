{if $field.type === 'select'}
  <label class='select-field'>
    <span>{$field.label}</span>
    <select {if $field.required} required {/if} name="{$field.name}">
      {foreach from=$field.availableValues item="label" key="value"}
        <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
      {/foreach}
    </select>
  </label>
{else if $field.type === 'radio-buttons'}
  <label class='radio-field'>
    <span>{$field.label}</span>
    {foreach from=$field.availableValues item="label" key="value"}
      <label>
        <input  name  = "{$field.name}"
                type  = "radio"
                value = "{$value}"
                {if $field.required} required {/if}
                {if $value eq $field.value} checked {/if}
        >
        {$label}
      </label>
    {/foreach}
  </label>
  {elseif $field.type === 'checkbox'}
  <label class='checkbox-field'>
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

{include file="_partials/form-errors.tpl" errors=$field.errors}
