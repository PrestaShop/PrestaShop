{extends "checkout/checkout-step.tpl"}

{block "step_content"}
  <div class="delivery-options-list">
    {if $delivery_options|count}
      <form id="delivery-method" method="post">
        {block name="delivery_options"}
          <div class="delivery-options">
            {foreach from=$delivery_options item=carrier key=carrier_id}
              <div>
                <div class="delivery-option">
                  <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if} />
                  <label for="delivery_option_{$carrier.id}">
                    <span>{$carrier.label}</span>
                    {if $carrier.logo}
                      <img src="{$carrier.logo}" alt="{$carrier.name}" />
                    {/if}
                  </label>
                </div>
              </div>
            {/foreach}
          </div>
        {/block}
        <div class='ps-hidden-by-js'>
          <button type="submit">{l s='Ok'}</button>
        </div>
      </form>

      <form>
        <input type="hidden" name="confirmDeliveryOption" value="1">
        <button type="submit" class="continue" name="continue" value="1">
            {l s='Continue'}
        </button>
      </form>
    {else}
      <p class="warning">{l s='Unfortunately, there are no carriers available for your delivery address.'}</p>
    {/if}
  </div>
{/block}
