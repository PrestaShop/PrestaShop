<section id='delivery-options'>

  <header>
    <h1 class="h3">{l s='Delivery options'}</h1>
  </header>

  <div class="delivery-option-list">

    {$HOOK_BEFORECARRIER}

    <ul>
      {foreach from=$carriers_available item=carrier}
        <li>
          <input type="radio" name="delivery_option" id="delivery_option_{$carrier.id}" value="{$carrier.id}" />
          <label for="delivery_option_{$carrier.id}"><img src="{$carrier.logo}" alt="{$carrier.name}" /> {$carrier.name} - {$carrier.delay} - {$carrier.price}</label>
        </li>
      {/foreach}
    </ul>

  </div>

</section>
