{extends file='customer/page.tpl'}

{block name='page_title'}
  {if $editing}
    {l s='Update your address' d='Shop.Theme.CustomerAccount'}
  {else}
    {l s='New address' d='Shop.Theme.CustomerAccount'}
  {/if}
{/block}

{block name='page_content'}
  <div class="address-form">
    {render template="customer/_partials/address-form.tpl" ui=$address_form}
  </div>
{/block}
