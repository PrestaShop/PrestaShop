{extends file='customer/page.tpl'}

{block name='page_title'}
  {if $editing}
    {l s='Update your address'}
  {else}
    {l s='New address'}
  {/if}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-address">
    <div class="address-form">
      {render template="customer/_partials/address-form.tpl" ui=$address_form}
    </div>
  </section>
{/block}
