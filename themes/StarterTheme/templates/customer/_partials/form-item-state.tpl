<select id="id_state" name="id_state" {if $required}required{/if}>
  {foreach $states as $state}
    <option value="{$state['id_state']}" {if $state.id_state == $sl_state}selected="selected"{/if}>
      {$state.name}
    </option>
  {/foreach}
</select>
