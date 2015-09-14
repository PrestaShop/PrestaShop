{extends "page.tpl"}

{block name="page_header_container"}{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-authentication">
    {block name="register_form_container"}
      {include file="customer/_partials/register-form.tpl"}
    {/block}
    {block name="login_form_container"}
      {include file="customer/_partials/login-form.tpl"}
    {/block}
  </section>
{/block}
