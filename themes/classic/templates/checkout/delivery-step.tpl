{extends file='checkout/checkout-step.tpl'}

{block name='step_content'}
  <div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier nofilter}
  </div>

  <div class="delivery-options-list">
    {if $delivery_options|count}
      <form id="delivery-method" method="post" class="clearfix">
        <div class="form-fields">
          {block name='delivery_options'}
            <div class="delivery-options">
              {foreach from=$delivery_options item=carrier key=carrier_id}
                <div>
                  <div class="delivery-option">
                    <span class="custom-radio pull-xs-left">
                      <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if} />
                      <span></span>
                    </span>
                    <label for="delivery_option_{$carrier.id}" class="delivery-option-2">
                      {if $carrier.logo}
                        <img src="{$carrier.logo}" alt="{$carrier.name}" />
                      {/if}
                      <span class="carrier-name">{$carrier.name}</span>
                      <span class="carrier-delay">{$carrier.delay}</span>
                      <span class="carrier-price">{$carrier.price}</span>
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
        <button type="submit" class="continue btn btn-primary pull-xs-right" name="confirmDeliveryOption" value="1">
          {l s='Continue'}
        </button>
      </form>
    {else}
      <p class="warning">{l s='Unfortunately, there are no carriers available for your delivery address.'}</p>
    {/if}
  </div>

  <div id="extra_carrier"></div>
{/block}
