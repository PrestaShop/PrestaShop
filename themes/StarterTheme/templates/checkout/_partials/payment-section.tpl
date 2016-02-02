<section id='payment-section'>
  <h1 class="h1">{l s='Payment Options'}</h1>

  {if !$all_conditions_approved}
    {l s='Please accept the Terms & Conditions before proceeding to payment.'}
  {/if}

  {include 'checkout/_partials/terms-and-conditions.tpl' assume_approved=$all_conditions_approved}
  {include 'checkout/_partials/payment-options.tpl'}
</section>
