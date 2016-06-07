{include file='_partials/form-errors.tpl' errors=$errors['']}

<form method="POST" action="{$action}" data-id-address="{$id_address}">
  <section class="form-fields">
    {block name='form_fields'}
      {foreach from=$formFields item="field"}
        {block name='form_field'}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}
  </section>

  <footer class="form-footer clearfix">
    <input type="hidden" name="submitAddress" value="1">
    {block name='form_buttons'}
      <button class="btn btn-primary pull-xs-right" type="submit" class="form-control-submit">
        {l s='Save' d='Shop.Theme.Actions'}
      </button>
    {/block}
  </footer>
</form>
