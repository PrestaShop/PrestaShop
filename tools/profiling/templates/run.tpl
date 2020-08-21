<div class="col-4">
  <table class="table table-condensed">
    <tr>
      <th>&nbsp;</th>
      <th>Time</th>
      <th>Cumulated Time</th>
      <th>Memory Usage</th>
      <th>Memory Peak Usage</th>
    </tr>

    {assign var="last" value=['time' => $run.startTime, 'memory_usage' => 0]}

    {foreach from=$run.profiler item=row}
      {if $row['block'] == 'checkAccess' && $row['time'] == $last['time']}
        {$continue}
      {/if}

      <tr>
        <td>{$row['block']}</td>
        <td>{load_time data=($row['time'] - $last['time'])} ms</td>
        <td>{load_time data=($row['time'] - $run.startTime)} ms</td>
        <td>{memory data=($row['memory_usage'] - $last['memory_usage'])} Mb</td>
        <td>{peak_memory data=($row['peak_memory_usage'])} Mb</td>
      </tr>

      {assign var="last" value=$row}
    {/foreach}
  </table>
</div>
