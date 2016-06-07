{extends file='page.tpl'}

{block name='page_title'}
  {l s='Create an account' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
    {block name='register_form_container'}
      {$hook_create_account_top nofilter}
      <section class="register-form">
        <p>{l s='Already have an account?' d='Shop.Theme.CustomerAccount'} <a href="{$urls.pages.authentication}">{l s='Log in instead!' d='Shop.Theme.CustomerAccount'}</a></p>
        {render file='customer/_partials/customer-form.tpl' ui=$register_form}
      </section>
    {/block}
{/block}
