{extends "page.tpl"}

{block name="page_title"}
  {l s='Your addresses'}
{/block}

{block name="page_content_container"}
<section id="content" class="page-content page-addresses">

  {foreach $customer.addresses as $address}
    {block name="customer_address"}
      {include file="customer/_partials/block-address.tpl" address=$address}
    {/block}
  {/foreach}

</section>
{/block}
