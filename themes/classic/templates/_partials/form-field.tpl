{if $field.type !== 'hidden'}
  <div class="form-group row">
    <label class="col-md-3 form-control-label{if $field.required} required{/if}">
      {if $field.type !== 'checkbox'}
        {$field.label}
      {/if}
    </label>
    <div class="col-md-6{if ($field.type === 'radio-buttons')} form-control-valign{/if}">
      {if $field.type === 'select'}
        <select {if $field.required} required {/if} name="{$field.name}" class="form-control form-control-select">
          <option value disabled selected>{l s='-- please choose --'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>
      {elseif $field.type === 'radio-buttons'}
        {foreach from=$field.availableValues item="label" key="value"}
          <label class="radio-inline">
            <span class="custom-radio">
              <input name = "{$field.name}" type = "radio" value = "{$value}" {if $field.required} required {/if} {if $value eq $field.value} checked {/if} />
              <span></span>
            </span>
            {$label}
          </label>
        {/foreach}
      {elseif $field.type === 'checkbox'}
        <span class="custom-checkbox">
          <input type="checkbox" value="{$field.value}" name="{$field.name}" {if $field.required} required {/if} />
          <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
          <label>{$field.label}</label>
        </span>
      {elseif $field.type === 'date'}
        <input type="text" class="form-control" placeholder="MM/DD/YYYY">
        <span class="form-control-comment">
          {l s='(Ex.:31/05/1970)'}
        </span>
      {else}
        <input type="{$field.type}" class="form-control" value="{$field.value}" name="{$field.name}" {if $field.required} required {/if} />
        <div class="tooltip bottom tooltip-bottom" role="tooltip">
          <div class="tooltip-arrow"></div>
          <div class="tooltip-inner">
            <i class="material-icons info">&#xE88F;</i>
            {l s='This field is required'}
          </div>
        </div>
      {/if}
    </div>
    {if $field.type === 'password'}
      <span class="col-md-3 form-control-comment">
        {l s='Five characters minimum'}
      </span>
    {elseif (!$field.required && $field.type !== 'radio-buttons' && $field.type !== 'checkbox') || $field.name === 'company' }
      <span class="col-md-3 form-control-comment">
        {l s='optional'}
      </span>
    {/if}
  </div>
{else}
  <input type="hidden" name="{$field.name}" value="{$field.value}">
{/if}

{include file='_partials/form-errors.tpl' errors=$field.errors}
