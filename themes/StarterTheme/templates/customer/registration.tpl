{extends file="page.tpl"}

{block name="page_header_container"}{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-authentication">
    {block name="register_form_container"}
      <header>
        <h1 class="h1">{l s='Create an account'}</h1>
      </header>
      <p>{l s='Already have an account?'} <a href="{$urls.pages.authentication}">{l s='Log in instead!'}</a></p>

      {$hook_create_account_top nofilter}
      <section class="register-form">
        {render file="customer/_partials/customer-form.tpl" ui=$register_form}
      </section>
    {/block}
  </section>
{/block}
