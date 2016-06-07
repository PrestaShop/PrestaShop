{extends 'customer/page.tpl'}

{block name='page_title'}
  {l s='Your personal information' d='Shop.Theme.CustomerAccount'}
{/block}

{* StarterTheme: Add confirmation/error messages *}

{block name='page_content'}
  {render file='customer/_partials/customer-form.tpl' ui=$customer_form}
{/block}
