<form id="conditions-to-approve" method="POST">
  <ul>
    {foreach from=$conditions_to_approve item="condition" key="condition_name"}
      <li>
        <label for="conditions_to_approve[{$condition_name}]">{$condition nofilter}</label>
        <input
          type="checkbox"
          name="conditions_to_approve[{$condition_name}]"
          id="conditions_to_approve[{$condition_name}]"
          value="1"
          required
          {if $approved_conditions.$condition_name} checked {/if}
        >
      </li>
    {/foreach}
  </ul>
  <input type="submit" value="{l s='I agree' js=1}">
</form>
