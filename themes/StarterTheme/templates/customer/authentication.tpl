{extends "page.tpl"}

{block name="page_header_container"}{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-authentication">
    {block name="login_form_container"}
      <header>
        <h1 class="h3">{l s='Log into your account'}</h1>
        <span><a href="{$urls.pages.register}" data-link-action="display-register-form">{l s='No account ? Create one here'}</a></span>
      </header>
      <section class="login-form">
        {block name="login_form"}
          {include file="customer/_partials/login-form.tpl"}
        {/block}
      </section>
    {/block}
  </section>
{/block}
