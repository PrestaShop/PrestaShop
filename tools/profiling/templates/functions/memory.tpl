{function name=memory}
  {if $data > 3}
    <span class="danger">{$data|string_format:"%0.2f"}</span>
  {elseif $data > 1}
    <span class="warning">{$data|string_format:"%0.2f"}</span>
  {elseif round($data, 2) > 0}
    <span class="success">{$data|string_format:"%0.2f"}</span>
  {else}
    <span class="success">-</span>
  {/if}
{/function}
