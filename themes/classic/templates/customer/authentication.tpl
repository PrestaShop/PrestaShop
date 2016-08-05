{extends file='page.tpl'}

{block name='page_title'}
  {l s='Log into your account' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
    {block name='login_form_container'}
      <section class="login-form">
        {render file='customer/_partials/login-form.tpl' ui=$login_form}
      </section>
      <hr/>
      {block name='display_after_login_form'}
        {hook h='displayCustomerLoginFormAfter'}
      {/block}
      <div class="no-account">
        <a href="{$urls.pages.register}" data-link-action="display-register-form">
          {l s='No account? Create one here' d='Shop.Theme.CustomerAccount'}
        </a>
      </div>
    {/block}
{/block}
