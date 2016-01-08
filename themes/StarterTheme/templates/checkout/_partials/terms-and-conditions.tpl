<form id="conditions-to-approve" method="POST">
  <ul>
    {foreach from=$conditions_to_approve item="condition" key="condition_name"}
      <li>
        <input
          type="checkbox"
          name="conditions_to_approve[{$condition_name}]"
          id="conditions_to_approve[{$condition_name}]"
          value="1"
          required
          {if $approved_conditions.$condition_name or $assume_approved} checked {/if}
          {if $assume_approved} disabled {/if}
        >
        <label for="conditions_to_approve[{$condition_name}]">{$condition nofilter}</label>
      </li>
    {/foreach}
  </ul>
  <div class='ps-hidden-by-js'>
    {if $assume_approved or $all_conditions_approved}
      <button id="disapprove-terms" type="submit">{l s="I've changed my mind, I don't agree with those terms any longer"}</button>
    {else}
      <button id="approve-terms" type="submit">{l s="I agree with all of the above"}</button>
    {/if}
  </div>
</form>
