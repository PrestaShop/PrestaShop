<form method="POST" action="{$action}">
  <section class="form-fields">
    {block "form_fields"}
      {foreach from=$formItems item="formItem"}
        {block name="form_field_{$formItem.name}"}
          {if $formItem.type !== 'select'}
            <label>
              <span>{$formItem.label}</span>
              <input  {if $formItem.required} required {/if}
                      name="{$formItem.name}"
                      type="{$formItem.type}"
                      value="{$formItem.value}"
              >
            </label>
          {else}
            <label>
              <span>{$formItem.label}</span>
              <select {if $formItem.required} required {/if} name="{$formItem.name}">
                {foreach from=$formItem.values item="label" key="value"}
                  <option value="{$value}" {if $value eq $formItem.value} selected {/if}>{$label}</option>
                {/foreach}
              </select>
            </label>
          {/if}
          {include file="_partials/form-field-errors.tpl" errors=$formItem.errors}
        {/block}
      {/foreach}
    {/block}
  </section>

  <footer class="form-footer">
    {if $back}
      <input type="hidden" name="back" value="{$back}">
    {/if}
    <input type="hidden" name="submitAddress" value="1">
    {block "form_buttons"}
      <button type="submit">{l s='Save'}</button>
    {/block}
  </footer>
</form>
