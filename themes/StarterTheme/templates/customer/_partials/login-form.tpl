{include file="_partials/form-errors.tpl" errors=$errors['']}

{* TODO StarterTheme: HOOKS!!! *}

<form action="{$action}" method="post">

  <section class="form-fields">
    {block "form_fields"}
      {foreach from=$formFields item="field"}
        {block "form_field"}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}

    <p class="lost_password">
      <a href="{$urls.pages.password}" rel="nofollow">
        {l s='Forgot your password?'}
      </a>
    </p>
  </section>

  <footer class="form-footer">
    <input type="hidden" name="submitLogin" value="1">
    {block "form_buttons"}
      <button type="submit">{l s='Sign in'}</button>
    {/block}
  </footer>

</form>
