{function name=sql_queries}
  {if $data > 150}
    <span class="danger">{$data} queries</span>
  {elseif $data > 100}
    <span class="warning">{$data} queries</span>
  {else}
    <span class="success">{$data} queries</span>
  {/if}
{/function}
