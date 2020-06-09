<div class="col-4">
  <table class="table table-condensed">
    <tr>
      <td>PrestaShop Version</td>
      <td>{$configuration.psVersion}</td>
    </tr>
    <tr>
      <td>PHP Version</td>
      <td>{$configuration.phpVersion}</td>
    </tr>
    <tr>
      <td>MySQL Version</td>
      <td>{$configuration.mysqlVersion}</td>
    </tr>
    <tr>
      <td>Memory Limit</td>
      <td>{$configuration.memoryLimit}</td>
    </tr>
    <tr>
      <td>Max Execution Time</td>
      <td>{$configuration.maxExecutionTime}s</td>
    </tr>
    <tr>
      <td>Smarty Cache</td>
      <td>
        {if $configuration.smartyCache}
          <span class="success">enabled</span>
        {else}
          <span class="error">disabled</span>
        {/if}
      </td>
    </tr>
    <tr>
      <td>Smarty Compilation</td>
      <td>
      {if $configuration.smartyCompilation == 0}
        <span class="success">never recompile</span>
      {elseif $configuration.smartyCompilation == 1}
        <span class="warning">auto</span>
      {else}
        <span class="red">force compile</span>
      {/if}
      </td>
    </tr>
  </table>
</div>
