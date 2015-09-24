{extends "page.tpl"}

{block name="page_title"}
  {l s='Add/edit your address'}
{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-address">

    {block name="address_form_container"}
      {include file="customer/_partials/address-form.tpl"}
    {/block}

  </section>
{/block}
