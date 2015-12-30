<form method="POST" action="{$action}">
  <section class="form-fields">
    {block "form_fields"}
      {foreach from=$formFields item="field"}
        {block "form_field"}
          {form_field field=$field}
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
