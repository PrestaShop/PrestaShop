<div class="advanced-payment-options">
  {foreach from=$payment_options item="options"}
    {foreach from=$options item="option"}
      <div>
        <div id="{$option.id}-container" class="advanced-payment-option">

          {* This is the way an option should be selected when Javascript is enabled *}
          <input class='ps-shown-by-js' id="{$option.id}" type="radio" name="advanced-payment-option" required>

          <label for="{$option.id}">
            <span>{$option.call_to_action_text}</span>
            {if $option.logo}
              <img src="{$option.logo}">
            {/if}
          </label>

        </div>
      </div>

      {if $option.additionalInformation}
        <div id="{$option.id}-additional-information" class="additional-information ps-hidden-by-js">
          {$option.additionalInformation nofilter}
        </div>
      {/if}

      <div id="pay-with-{$option.id}-form" class="payment-option-form ps-hidden-by-js">
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
  <div class='ps-shown-by-js'>
    <button type="submit" {if !$selected_payment_option} disabled {/if}>
      {l s='Order with an obligation to pay'}
    </button>
  </div>
</div>
