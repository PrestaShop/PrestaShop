{include file='_partials/form-errors.tpl' errors=$errors['']}

<form method="POST" action="{$action}" data-toggle="validator">
  <section class="form-fields">
    {block name='form_fields'}
      {foreach from=$formFields item="field"}
        {block name='form_field'}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}
  </section>

  <footer class="form-footer">
    <input type="hidden" name="submitAddress" value="1">
    {block name='form_buttons'}
      <button type="submit" class="btn btn-primary">{l s='Save'}</button>
    {/block}
  </footer>
</form>
