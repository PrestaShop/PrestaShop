{function name=table}
  {if $data > 30}
    <span class="danger">{$data}</span>
  {elseif $data > 20}
    <span class="warning">{$data}</span>
  {else}
    <span class="success">{$data}</span>
  {/if}
{/function}
