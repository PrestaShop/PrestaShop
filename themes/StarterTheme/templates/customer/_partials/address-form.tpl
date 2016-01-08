{include file="_partials/form-errors.tpl" errors=$errors['']}

<form method="POST" action="{$action}">
  <section class="form-fields">
    {block name="form_fields"}
      {foreach from=$formFields item="field"}
        {block name="form_field"}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}
  </section>

  <footer class="form-footer">
    <input type="hidden" name="submitAddress" value="1">
    {block "form_buttons"}
      <button type="submit">{l s="Save"}</button>
    {/block}
  </footer>
</form>
