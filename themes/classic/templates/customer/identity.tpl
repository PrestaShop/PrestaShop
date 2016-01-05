{extends "customer/page.tpl"}

{block name="page_title"}
  {l s='Your personal information'}
{/block}

{* StarterTheme: Add confirmation/error messages *}

{block name="page_content"}
  {render ui=$customer_form file="customer/_partials/customer-form.tpl"}
{/block}
