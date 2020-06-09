{function name=load_time}
  {if $data > 1.6}
    <span class="danger">{round($data * 1000)}</span>
  {elseif $data > 0.8}
    <span class="warning">{round($data * 1000)}</span>
  {else}
    <span class="success">{round($data * 1000)}</span>
  {/if}
{/function}
