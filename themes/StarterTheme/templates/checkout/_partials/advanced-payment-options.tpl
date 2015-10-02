<div class="advanced-payment-options">
  {foreach from=$payment_options item="options"}
    {foreach from=$options item="option"}
      <div id="{$option.id}-container" class="advanced-payment-option">

        {* This is the form that needs to be submitted when the order is confirmed by the customer *}
        <form id="pay-with-{$option.id}-form" method="{$option.method}" action="{$option.action}">
          <button style="display:none" id="pay-with-{$option.id}" type="submmit"></button>
        </form>

        {* This is the way an option should be selected when Javascript is enabled *}
        <input class='ps-shown-by-js' id="{$option.id}" type="radio" name="advanced-payment-option" required>

        {* When Javascript is disabled, we emulate a radio with buttons *}
        <form class='ps-hidden-by-js' method="POST">
          {if $selected_payment_option == $option.id}
            <button type="button" disabled class="selected-payment-option">{l s='Selected'}</button>
          {else if $all_conditions_approved}
            <button type="submit" name="select_payment_option" value="{$option.id}">{l s='Select'}</button>
          {else}
            <p class="warning">{l s='Please accept the Terms & Conditions before proceeding to payment.'}</p>
          {/if}
        </form>

        <label for="{$option.id}">
          <span>{$option.call_to_action_text}</span>
          {if $option.logo}
            <img src="{$option.logo}">
          {/if}
        </label>
      </div>
    {/foreach}
  {/foreach}
</div>

<div id="payment-confirmation">
  {if $selected_payment_option}
    {* Case for Javascript disabled *}
    <label for="pay-with-{$selected_payment_option}">{l s='Order with an obligation to pay'}</label>
  {else}
    {* Case for Javascript enabled *}
    <div class='ps-shown-by-js'>
      <button type="submit" {if !$selected_payment_option} disabled {/if}>
        {l s='Order with an obligation to pay'}
      </button>
    </div>
  {/if}
</div>
