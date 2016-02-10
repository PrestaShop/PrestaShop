{if isset($confirmations) && count($confirmations) && $confirmations}
  <div class="bootstrap">
    <div class="alert alert-success" style="display:block;">
      {foreach $confirmations as $conf}
        {$conf}
      {/foreach}
    </div>
  </div>
{/if}
