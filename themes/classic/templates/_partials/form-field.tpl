{if $field.type == 'hidden'}

  <input type="hidden" name="{$field.name}" value="{$field.value}">

{else}

  <div class="form-group row {if !empty($field.errors)}has-error{/if}">
    <label class="col-md-3 form-control-label{if $field.required} required{/if}">
      {if $field.type !== 'checkbox'}
        {$field.label}
      {/if}
    </label>
    <div class="col-md-6{if ($field.type === 'radio-buttons')} form-control-valign{/if}">

      {if $field.type === 'select'}

        <select class="form-control form-control-select" name="{$field.name}" {if $field.required}required{/if}>
          <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>

      {elseif $field.type === 'countrySelect'}

        <select
          class="form-control form-control-select js-country"
          name="{$field.name}"
          {if $field.required}required{/if}
        >
          <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>

      {elseif $field.type === 'radio-buttons'}

        {foreach from=$field.availableValues item="label" key="value"}
          <label class="radio-inline">
            <span class="custom-radio">
              <input
                name="{$field.name}"
                type="radio"
                value="{$value}"
                {if $field.required}required{/if}
                {if $value eq $field.value} checked {/if}
              >
              <span></span>
            </span>
            {$label}
          </label>
        {/foreach}

      {elseif $field.type === 'checkbox'}

        <span class="custom-checkbox">
          <input name="{$field.name}" type="checkbox" value="{$field.value}" {if $field.required}required{/if}>
          <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
          <label>{$field.label}</label>
        </span>

      {elseif $field.type === 'date'}

        <input class="form-control" type="text" placeholder="DD/MM/YYYY">
        <span class="form-control-comment">
          {l s='(Ex.:31/05/1970)' d='Shop.Forms.Help'}
        </span>

      {elseif $field.type === 'password'}

        <div class="input-group js-parent-focus">
          <input
            class="form-control js-child-focus js-visible-password"
            name="{$field.name}"
            type="password"
            value=""
            pattern=".{literal}{{/literal}5,{literal}}{/literal}"
            {if $field.required}required{/if}
          >
          <span class="input-group-btn">
            <button
              class="btn"
              type="button"
              data-action="show-password"
              data-text-show="{l s='Show' d='Shop.Theme.Actions'}"
              data-text-hide="{l s='Hide' d='Shop.Theme.Actions'}"
            >
              {l s='Show' d='Shop.Theme.Actions'}
            </button>
          </span>
        </div>
      {else}

        <input
          class="form-control"
          name="{$field.name}"
          type="{$field.type}"
          value="{$field.value}"
          {if $field.maxLength}maxlength="{$field.maxLength}"{/if}
          {if $field.required}required{/if}
        >

      {/if}

      {include file='_partials/form-errors.tpl' errors=$field.errors}

    </div>

    <div class="col-md-3 form-control-comment">
      {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
       {l s='Optional' d='Shop.Forms.Labels'}
      {/if}
    </div>
  </div>

{/if}

