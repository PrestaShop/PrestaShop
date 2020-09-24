{function name=time}
  {if $data > 4}
    <span class="danger">{$data}</span>
  {elseif $data > 2}
    <span class="warning">{$data}</span>
  {else}
    <span class="success">{$data}</span>
  {/if}
{/function}
