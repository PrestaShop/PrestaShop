{extends 'customer/page.tpl'}

{block name='page_title'}
  {l s='Your personal information'}
{/block}

{* StarterTheme: Add confirmation/error messages *}

{block name='page_content_container'}
  <section id="content" class="page-content page-identity">
  	{render file='customer/_partials/customer-form.tpl' ui=$customer_form}
  </section>
{/block}
