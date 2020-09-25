<div class="col-4">
  <table class="table table-condensed">
    <thead>
      <tr>
        <th>Module</th>
        <th>Time</th>
        <th>Memory Usage</th>
      </tr>
    </thead>

    <tbody>
      {foreach $modules.perfs as $modulePerfs}
        <tr>
          <td>
            {$modulePerfs['module']}
          </td>
          <td>
            {load_time data=$modulePerfs['time']}
          </td>
          <td>
            {memory data=$modulePerfs['memory']}
          </td>
        </tr>
      {/foreach}
    </tbody>

    <tfoot>
      <tr>
        <th><b>{$modules.perfs|count} module(s)</b></th>
        <th>{load_time data=$modules.totalHooksTime}</th>
        <th>{memory data=$modules.totalHooksMemory}</th>
      </tr>
    </tfoot>
  </table>
</div>
