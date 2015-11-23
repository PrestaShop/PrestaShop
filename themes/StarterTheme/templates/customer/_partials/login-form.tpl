<form action="{$urls.pages.authentication}" method="post">
  <header>
    <h1 class="h3">{l s='Log into your account'}</h1>
    <span><a href="{$urls.pages.register}" data-link-action="display-register-form">{l s='No account ? Create one here'}</a></span>
  </header>

  <section class="form-fields">

    <label>
      <span>{l s='Email address'}</span>
      <input type="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" />
    </label>

    <label>
      <span>{l s='Password'}</span>
      <input type="password" data-validate="isPasswd" name="passwd" value="" />
    </label>
    <p class="lost_password"><a href="{$urls.pages.password}" rel="nofollow">{l s='Forgot your password?'}</a></p>

  </section>

  <footer class="form-footer">
    <input type="hidden" name="back" value="{$back}">
    <input type="hidden" name="SubmitLogin" value="1">

    <button type="submit">{l s='Sign in'}</button>
  </footer>

</form>
