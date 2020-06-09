{function name=rows_browsed}
  {if $data > 400}
    <span class="danger">{$data} rows browsed</span>
  {elseif $data > 100}
    <span class="warning">{$data} rows browsed</span>
  {else}
    <span class="success">{$data} rows browsed</span>
  {/if}
{/function}
