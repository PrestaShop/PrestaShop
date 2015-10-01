<section id='payment-options'>
  <h1 class="h3">{l s='Payment Options'}</h1>

  {if $advanced_payment_api}
    TODO
  {else}
    {include 'checkout/_partials/terms-and-conditions.tpl'}
    {if $all_conditions_approved}
      {$payment_options nofilter}
    {else}
      {l s='Please accept the Terms & Conditions before proceeding to payment.'}
    {/if}
  {/if}
</section>
