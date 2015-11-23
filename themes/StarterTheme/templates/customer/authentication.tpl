{extends "page.tpl"}

{block name="page_header_container"}{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-authentication">
    {block name="login_form_container"}
      <section class="login-form">
        {block name="login_form"}
          {include file="customer/_partials/login-form.tpl"}
        {/block}
      </section>
    {/block}
  </section>
{/block}
