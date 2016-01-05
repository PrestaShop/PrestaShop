{extends "page.tpl"}

{block name="page_title"}
  {l s='Forgot your password?'}
{/block}

{block name="page_content_container"}
  <section class="password-form">
    <form action="{$urls.pages.password}" method="post">

      <header>
        <p>{l s='Please enter the email address you used to register. You will receive a temporary link to reset your password.'}</p>
      </header>

      <section class="form-fields">

        <label>
          <span>{l s='Email address'}</span>
          <input type="email" name="email" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" />
        </label>

      </section>

      <footer class="form-footer">
        <button type="submit" name="submit">
          {l s='Send reset link'}
        </button>
      </footer>

    </form>
  </section>
{/block}

{block name="page_footer"}
  <ul>
    <li><a href="{$urls.pages.authentication}">{l s='Back to Login'}</a></li>
  </ul>
{/block}
