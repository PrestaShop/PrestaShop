{function name=peak_memory}
  {$data = round($data / 1048576, 2)}

  {if $data > 16}
    <span class="danger">{$data|string_format:"%0.1f"}</span>
  {elseif $data > 12}
    <span class="warning">{$data|string_format:"%0.1f"}</span>
  {else}
    <span class="success">{$data|string_format:"%0.1f"}</span>
  {/if}
  Mb
{/function}
