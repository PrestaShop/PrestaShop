{include file='_partials/form-errors.tpl' errors=$errors['']}

{* TODO StarterTheme: HOOKS!!! *}

<form action="{$action}" method="post">

  <section>
    {block name='form_fields'}
      {foreach from=$formFields item="field"}
        {block name='form_field'}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}
  </section>

  <footer class="form-footer">
    <input type="hidden" name="submitLogin" value="1">
    {block name='form_buttons'}
      <button type="submit" class="btn btn-primary">{l s='Sign in'}</button>
    {/block}
    <div class="forgot-password">
      <a href="{$urls.pages.password}" rel="nofollow">
        {l s='Forgot your password?'}
      </a>
    </div>
  </footer>

</form>

