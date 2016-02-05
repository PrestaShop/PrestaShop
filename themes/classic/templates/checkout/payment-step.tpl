{extends file='checkout/checkout-step.tpl'}

{block name='step_content'}

  {if $conditions_to_approve|count}
    <p class="ps-hidden-by-js">
      {* At the moment, we're not showing the checkboxes when JS is disabled
         because it makes ensuring they were checked very tricky and overcomplicates
         the template. Might change later.
      *}
      {l s='By confirming your order, I certify that I have read and agree with all of the conditions below:'}
    </p>

    <form id="conditions-to-approve" method="GET">
      <ul>
        {foreach from=$conditions_to_approve item="condition" key="condition_name"}
          <li class="_display-table">
            <div class="_display-table-cell">
              <input  id    = "conditions_to_approve[{$condition_name}]"
                      name  = "conditions_to_approve[{$condition_name}]"
                      required
                      type  = "checkbox"
                      value = "1"
                      class = "ps-shown-by-js _margin-right-small"
              >
            </div>
            <div class="_display-table-cell">
              <label for="conditions_to_approve[{$condition_name}]">
                {$condition nofilter}
              </label>
            </div>
          </li>
        {/foreach}
      </ul>
    </form>
  {/if}

  <div class="payment-options">
    {foreach from=$payment_options item="module_options"}
      {foreach from=$module_options item="option"}
        <div>
          <div id="{$option.id}-container" class="payment-option clearfix _display-table">

            {* This is the way an option should be selected when Javascript is enabled *}
            <input class="ps-shown-by-js _margin-right-small" id="{$option.id}" type="radio" name="payment-option" required {if $selected_payment_option == $option.id} checked {/if}>
            {* This is the way an option should be selected when Javascript is disabled *}
            <form method="GET" class="ps-hidden-by-js">
              {if $option.id === $selected_payment_option}
                {l s='Selected'}
              {else}
                <button class="ps-hidden-by-js" type="submit" name="select_payment_option" value="{$option.id}">
                  {l s='Choose'}
                </button>
              {/if}
            </form>

            <label for="{$option.id}" class="_margin-right-small">
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
      <button type="submit" {if !$selected_payment_option} disabled {/if} class="submit-button center-block">
        {l s='Order with an obligation to pay'}
      </button>
    </div>
    <div class="ps-hidden-by-js">
      {if $selected_payment_option and $all_conditions_approved}
        <label for="pay-with-{$selected_payment_option}">{l s='Order with an obligation to pay'}</label>
      {/if}
    </div>
  </div>
{/block}
