{extends file='checkout/_partials/order-confirmation-table.tpl'}

{block name='order-items-table-head'}
<div id="order-items" class="col-md-12">
  <h3 class="card-title h3">
    {if $products_count == 1}
       {l s='%s item in your cart' sprintf=$products_count d='Shop.Theme.Checkout'}
    {else}
       {l s='%s items in your cart' sprintf=$products_count d='Shop.Theme.Checkout'}
    {/if}
  	<a href="{url entity=cart params=['action' => 'show']}"><span class="step-edit"><i class="material-icons edit">mode_edit</i> edit</span></a>
  </h3>
{/block}
