{if $overridden_terms}

  {$overridden_terms}

{else}

  <label for="cgv">
    <span>
      {l s='I agree to the terms of service and will adhere to them unconditionally.'}
      <a href="{$link_conditions}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)'}</a>
    </span>
    <input type="checkbox" name="cgv" id="cgv" value="1" />
  </label>

{/if}
