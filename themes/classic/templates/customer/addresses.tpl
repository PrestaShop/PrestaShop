{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Your addresses'}
{/block}

{block name='page_content_container'}
<section id="content" class="page-content page-addresses">

  {foreach $customer.addresses as $address}
    <div class="col-lg-4 col-md-6 col-sm-6">
    {block name='customer_address'}
      {include file='customer/_partials/block-address.tpl' address=$address}
    {/block}
    </div>
  {/foreach}
  <div class="clearfix"></div>
  <footer>
    <a href="{$urls.pages.address}" data-link-action="add-address" class="btn btn-primary">
      {l s='Create new address'}
    </a>
  </footer>

</section>
{/block}
