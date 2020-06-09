{function name=mysql_version}
  {if version_compare($version, '5.5') < 0}
    <span class="danger">{$data} (Upgrade strongly recommended)</span>
  {elseif version_compare($version, '5.6') < 0}
    <span class="warning">{$data} (Consider upgrading)</span>
  {else}
    <span class="success">{$data} (OK)</span>
  {/if}
{/function}
