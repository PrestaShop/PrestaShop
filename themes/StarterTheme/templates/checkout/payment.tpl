<section id='payment-options' data-uses-advanced-payment-api="{$advanced_payment_api}">
  <h1 class="h3">{l s='Payment Options'}</h1>

  {if !$all_conditions_approved}
    {l s='Please accept the Terms & Conditions before proceeding to payment.'}
  {/if}

  {if $advanced_payment_api}
    {include 'checkout/_partials/terms-and-conditions.tpl' assume_approved=$all_conditions_approved}
    {include 'checkout/_partials/advanced-payment-options.tpl'}
  {else}
    {include 'checkout/_partials/terms-and-conditions.tpl' assume_approved=false}
    {if $all_conditions_approved}
      {$payment_options nofilter}
    {else}
      {l s='Please accept the Terms & Conditions before proceeding to payment.'}
    {/if}
  {/if}
</section>
