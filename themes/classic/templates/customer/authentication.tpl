{extends file='page.tpl'}

{block name='page_header_container'}{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-authentication">
    {block name='login_form_container'}
      <header>
        <h1>{l s='Log into your account'}</h1>
      </header>
      <section class="login-form">
        {render file='customer/_partials/login-form.tpl' ui=$login_form}
      </section>
      <hr/>
      <div class="no-account">
        <a href="{$urls.pages.register}" data-link-action="display-register-form">
          {l s='No account ? Create one here'}
        </a>
      </div>
    {/block}
  </section>
{/block}
