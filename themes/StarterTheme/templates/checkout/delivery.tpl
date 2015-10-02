<section id='delivery-options'>

  <header>
    <h1 class="h3">{l s='Delivery options'}</h1>
  </header>

  <div class="delivery-options-list">

    {$HOOK_BEFORECARRIER}

    <form id="delivery-method" method="post">
      {block name="delivery_options"}
        <ul>
          {foreach from=$carriers_available item=carrier key=carrier_id}
            <li>
              <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if} />
              <label for="delivery_option_{$carrier.id}"><img src="{$carrier.logo}" alt="{$carrier.name}" /> {$carrier.name} - {$carrier.delay} - {$carrier.price}</label>
            </li>
          {/foreach}
        </ul>
      {/block}
      <button type="submit">{l s='Ok'}</button>
    </form>

  </div>

</section>
