{if $field.type !== 'hidden'}
  <div class="form-group row">
    <label class="col-md-3 form-control-label{if $field.required} required{/if}">{$field.label}</label>
    <div class="col-md-9{if ($field.type === 'checkbox' || $field.type === 'radio-buttons')} form-control-valign{/if}">
      {if $field.type === 'select'}
        <select {if $field.required} required {/if} name="{$field.name}" class="form-control">
          <option value disabled selected>{l s='-- please choose --'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>
      {else if $field.type === 'radio-buttons'}
        {foreach from=$field.availableValues item="label" key="value"}
          <label class="radio-inline">
            <input name = "{$field.name}" type = "radio" value = "{$value}" {if $field.required} required {/if} {if $value eq $field.value} checked {/if} />
            {$label}
          </label>
        {/foreach}
      {elseif $field.type === 'checkbox'}
        <input type="checkbox" value="{$field.value}" name="{$field.name}" {if $field.required} required {/if} />
      {else}
        <input type="{$field.type}" class="form-control" value="{$field.value}" name="{$field.name}" {if $field.required} required {/if} />
        {if $field.type === 'password'}
          <small>{l s='Five characters minimum'}</small>
        {elseif $field.name === 'company'}
          <small>{l s='Optional'}</small>
        {/if}
      {/if}
    </div>
  </div>
{else}
  <input type="hidden" name="{$field.name}" value="{$field.value}">
{/if}

{include file='_partials/form-errors.tpl' errors=$field.errors}
