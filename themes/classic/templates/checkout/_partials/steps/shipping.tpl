{extends file='checkout/_partials/steps/checkout-step.tpl'}

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
                  <div class="delivery-option row">
                    <div class="col-md-1">
                      <span class="custom-radio pull-xs-left">
                        <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if}>
                        <span></span>
                      </span>
                    </div>
                    <div class="col-md-11">
                      <div class="row">
                        <label for="delivery_option_{$carrier.id}" class="delivery-option-2">
                          <div class="col-md-3">
                            <div class="logo-container">
                              {if $carrier.logo}
                                <img src="{$carrier.logo}" alt="{$carrier.name}">
                                {else}
                                &nbsp;
                              {/if}
                            </div>
                          </div>
                          <div class="col-md-4">
                            <span class="carrier-name">{$carrier.name}</span>
                          </div>
                          <div class="col-md-4">
                            <span class="carrier-delay">{$carrier.delay}</span>
                          </div>
                          <div class="col-md-1">
                            <span class="carrier-price">{$carrier.price}</span>
                          </div>
                        </label>
                      </div>
                    </div>
                  </div>
              {/foreach}
            </div>
          {/block}
          <div class="order-options">
            {if $recyclablePackAllowed}
              <label>
                <input type="checkbox" name="recyclable" value="1" {if $recyclable} checked {/if}>
                <span>{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</span>
              </label>
            {/if}
            {if $gift.allowed}
              <label>
                <input type="checkbox" name="gift" value="1" {if $gift.isGift} checked {/if}>
                <span>{$gift.label}</span>
              </label>
              <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
              <textarea rows="2" cols="120" id="gift_message" name="gift_message">{$gift.message}</textarea>
            {/if}
          </div>
        </div>
        <button type="submit" class="continue btn btn-primary pull-xs-right" name="confirmDeliveryOption" value="1">
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>
    {else}
      <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
    {/if}
  </div>

  <div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier nofilter}
  </div>

  <div id="extra_carrier"></div>
{/block}
