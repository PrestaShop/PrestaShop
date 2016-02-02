{foreach from=$errors[null] item=error}
  <p>{$error}</p>
{/foreach}

{* TODO StarterTheme: HOOKS!!! *}

<form action="{$action}" method="post">

  <section class="form-fields">
    {block "form_fields"}
      <label>
        <span>{l s='Email address'}</span>
        <input type="email" name="email" value="{$email}">
      </label>
      {include file="_partials/form-field-errors.tpl" errors=$errors.email}

      <label>
        <span>{l s='Password'}</span>
        <input type="password" name="password" value="">
      </label>
      {include file="_partials/form-field-errors.tpl" errors=$errors.password}

      <p class="lost_password">
        <a href="{$urls.pages.password}" rel="nofollow">
          {l s='Forgot your password?'}
        </a>
      </p>
    {/block}
  </section>

  <footer class="form-footer">
    {if $back}
      <input type="hidden" name="back" value="{$back}">
    {/if}
    <input type="hidden" name="submitLogin" value="1">
    {block "form_buttons"}
      <button type="submit">{l s='Sign in'}</button>
    {/block}
  </footer>

</form>
