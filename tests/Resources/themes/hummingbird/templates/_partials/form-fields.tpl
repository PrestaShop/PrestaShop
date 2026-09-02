{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{if $field.type == 'hidden'}

  {block name='form_field_item_hidden'}
    <input type="hidden" name="{$field.name}" value="{$field.value}">
  {/block}

{else}

  <div class="mb-3{if !empty($field.errors)} has-error{/if}">
    {if ($field.type !== 'checkbox')}
      <label class="form-label{if $field.required} required{/if}" for="field-{$field.name}">
        {if $field.type !== 'checkbox'}
          {$field.label}
        {/if}
      </label>
    {/if}

    {if $field.type === 'select'}

      {block name='form_field_item_select'}
        <select class="form-select" name="{$field.name}" {if $field.required}required{/if}>
          <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>
      {/block}

    {elseif $field.type === 'countrySelect'}

      {block name='form_field_item_country'}
        <select
        class="form-select js-country"
        name="{$field.name}"
        {if $field.required}required{/if}
        >
          <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
          {foreach from=$field.availableValues item="label" key="value"}
            <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
          {/foreach}
        </select>
      {/block}

    {elseif $field.type === 'radio-buttons'}

      {block name='form_field_item_radio'}
        <div>
          {foreach from=$field.availableValues item="label" key="value"}
            <div class="form-check form-check-inline">
              <input
                class="form-check-input"
                type="radio"
                name="{$field.name}"
                id="field-{$field.name}_{$value}"
                value="{$value}"
                {if $field.required}required{/if}
                {if $value eq $field.value} checked {/if}
              >
              <label class="form-check-label" for="field-{$field.name}_{$value}">
                {$label}
              </label>
            </div>
          {/foreach}
        </div>
      {/block}

    {elseif $field.type === 'checkbox'}

      {block name='form_field_item_checkbox'}
        <div class="form-check">
          <input
            class="form-check-input"
            name="{$field.name}"
            type="checkbox"
            value="1"
            id="field-{$field.name}"
            value="1" {if $field.value}checked="checked"{/if}
            {if $field.required}required{/if}
          >
          <label class="form-check-label" for="field-{$field.name}">
            {$field.label nofilter}
          </label>
        </div>
      {/block}

    {elseif $field.type === 'date'}

      {block name='form_field_item_date'}
        <input name="{$field.name}" class="form-control" type="date" value="{$field.value}"{if isset($field.availableValues.placeholder)} placeholder="{$field.availableValues.placeholder}" aria-label="{$field.availableValues.placeholder}"{/if}>
        {if isset($field.availableValues.comment)}
          <span class="form-text">
            {$field.availableValues.comment}
          </span>
        {/if}
      {/block}

    {elseif $field.type === 'birthday'}

      {block name='form_field_item_birthday'}
        <div class="js-parent-focus">
          {html_select_date
          field_order=DMY
          time={$field.value}
          field_array={$field.name}
          prefix=false
          reverse_years=true
          field_separator='<br>'
          day_extra='class="form-select"'
          month_extra='class="form-select"'
          year_extra='class="form-select"'
          day_empty={l s='-- day --' d='Shop.Forms.Labels'}
          month_empty={l s='-- month --' d='Shop.Forms.Labels'}
          year_empty={l s='-- year --' d='Shop.Forms.Labels'}
          start_year={'Y'|date}-100 end_year={'Y'|date}
          }
        </div>
      {/block}

    {elseif $field.type === 'password'}

      {block name='form_field_item_password'}

        <div class="input-group password-field js-parent-focus">
          <input
            class="form-control js-child-focus js-visible-password"
            name="{$field.name}"
            id="field-{$field.name}"
            type="password"
            {if $field.autocomplete}autocomplete="{$field.autocomplete}"{/if}
            value=""
            pattern=".{literal}{{/literal}5,{literal}}{/literal}"
            {if $field.required}required{/if}
            {if isset($configuration.password_policy.minimum_length)}data-minlength="{$configuration.password_policy.minimum_length}"{/if}
            {if isset($configuration.password_policy.maximum_length)}data-maxlength="{$configuration.password_policy.maximum_length}"{/if}
            {if isset($configuration.password_policy.minimum_score)}data-minscore="{$configuration.password_policy.minimum_score}"{/if}
            data-bs-placement="top"
            data-bs-trigger="manual"
          >

          <button
            class="btn btn-primary"
            type="button"
            data-action="show-password"
            data-text-show="{l s='Show Password' d='Shop.Theme.Actions'}"
            data-text-hide="{l s='Hide Password' d='Shop.Theme.Actions'}"
            aria-label="{l s='Show Password' d='Shop.Theme.Actions'}"
            aria-expanded="false"
          >
            <i class="material-icons">visibility</i>
          </button>
        </div>
      {/block}

    {elseif $field.type === 'textarea'}

      {block name='form_field_item_textarea'}
          <textarea
            id="field-{$field.name}"
            class="form-control"
            name="{$field.name}"
            {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder}"{/if}
            {if $field.maxLength}maxlength="{$field.maxLength}"{/if}
            {if $field.required}required{/if}
            {if isset($field.availableValues.rows)}rows="{$field.availableValues.rows}"{/if}
            {if isset($field.availableValues.cols)}cols="{$field.availableValues.cols}"{/if}
          >{$field.value|default}</textarea>
          {if isset($field.availableValues.comment)}
            <span class="form-text">
              {$field.availableValues.comment}
            </span>
          {/if}
      {/block}

    {else}

      {block name='form_field_item_other'}
        <input
          class="form-control"
          name="{$field.name}"
          id="field-{$field.name}"
          type="{$field.type}"
          value="{$field.value}"
          {if $field.autocomplete}autocomplete="{$field.autocomplete}"{/if}
          {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder}"{/if}
          {if $field.maxLength}maxlength="{$field.maxLength}"{/if}
          {if !empty($field.minLength)}minlength="{$field.minLength}"{/if}
          aria-label="{$field.name}"
          {if $field.required}required{/if}
        >
        {if isset($field.availableValues.comment)}
          <span class="form-text">
            {$field.availableValues.comment}
          </span>
        {/if}
      {/block}

    {/if}

    {block name='form_field_errors'}
      {include file='_partials/form-errors.tpl' errors=$field.errors}
    {/block}

    {block name='form_field_comment'}
      {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
        <div class="form-text">{l s='Optional' d='Shop.Forms.Labels'}</div>
      {/if}
    {/block}
  </div>
{/if}
