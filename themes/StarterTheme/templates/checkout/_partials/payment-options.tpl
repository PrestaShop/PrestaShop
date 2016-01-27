<div class="advanced-payment-options" data-checkout-step-status="{$status}">
  {foreach from=$payment_options item="options"}
    {foreach from=$options item="option"}
      <div>
        <div id="{$option.id}-container" class="advanced-payment-option">

          {* This is the way an option should be selected when Javascript is enabled *}
          <input class="ps-shown-by-js" id="{$option.id}" type="radio" name="advanced-payment-option" required>
          {* This is the way an option should be selected when Javascript is disabled *}
          <form method="POST">
            {if $option.id === $selected_payment_option}
              {l s="Selected"}
            {else if $all_conditions_approved}
              <button class="ps-hidden-by-js" type="submit" name="select_payment_option" value="{$option.id}">
                {l s="Choose"}
              </button>
            {/if}
          </form>

          <label for="{$option.id}">
            <span>{$option.call_to_action_text}</span>
            {if $option.logo}
              <img src="{$option.logo}">
            {/if}
          </label>

        </div>
      </div>

      {if $option.additionalInformation}
        <div
          id="{$option.id}-additional-information"
          class="js-additional-information {if $option.id != $selected_payment_option} ps-hidden {/if}"
        >
          {$option.additionalInformation nofilter}
        </div>
      {/if}

      <div
        id="pay-with-{$option.id}-form"
        class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}"
      >
        {if $option.form}
          {$option.form nofilter}
        {else}
          <form id="payment-form" method="{$option.method}" action="{$option.action}">
            {foreach from=$option.inputs item=input}
              <input type="{$input.type}" name="{$input.name}" value="{$input.value}">
            {/foreach}
            <button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
          </form>
        {/if}
      </div>
    {/foreach}
  {/foreach}
</div>

<div id="payment-confirmation">
  <div class="ps-shown-by-js">
    <button type="submit" {if !$selected_payment_option} disabled {/if}>
      {l s='Order with an obligation to pay'}
    </button>
  </div>
  <div class="ps-hidden-by-js">
    {if $selected_payment_option and $all_conditions_approved}
      <label for="pay-with-{$selected_payment_option}">{l s='Order with an obligation to pay'}</label>
    {/if}
  </div>
</div>
