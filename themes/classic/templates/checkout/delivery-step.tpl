{extends "checkout/checkout-step.tpl"}

{block "step_content"}
  <div class="delivery-options-list">
    {if $delivery_options|count}
      <form id="delivery-method" method="post">
        <div class="form-fields">
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
          <div class="order-options">
            {if $recyclablePackAllowed}
              <label>
                <input type="checkbox" name="recyclable" value="1" {if $recyclable} checked {/if}>
                <span>{l s='I would like to receive my order in recycled packaging.'}</span>
              </label>
            {/if}
            {if $gift.allowed}
              <label>
                <input type="checkbox" name="gift" value="1" {if $gift.isGift} checked {/if}>
                <span>{$gift.label}</span>
              </label>
              <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:'}</label>
              <textarea rows="2" cols="120" id="gift_message" name="gift_message">{$gift.message}</textarea>
            {/if}
          </div>
        </div>
        <button type="submit" class="continue" name="confirmDeliveryOption" value="1">
          {l s='Continue'}
        </button>
      </form>
    {else}
      <p class="warning">{l s='Unfortunately, there are no carriers available for your delivery address.'}</p>
    {/if}
  </div>
{/block}
