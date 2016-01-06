{foreach from=$errors[''] item=error}
  <p>{$error}</p>
{/foreach}

<form action="{$action}" id="customer-form" method="post">

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
    <input type="hidden" name="submitCreate" value="1">
    {block "form_buttons"}
      <button type="submit">
        {l s='Save'}
      </button>
    {/block}
  </footer>

</form>
