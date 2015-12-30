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
    <span>{$field.label}</span>
    <input  {if $field.required} required {/if}
            name="{$field.name}"
            type="{$field.type}"
            value="{$field.value}"
    >
  </label>
{/if}

{if $field.errors|count}
  <ul>
    {foreach $field.errors as $error}
      <li>{$error nofilter}</li>
    {/foreach}
  </ul>
{/if}
