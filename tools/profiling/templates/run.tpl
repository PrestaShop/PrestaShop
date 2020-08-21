<div class="col-4">
  <table class="table table-condensed">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th>Time</th>
        <th>Cumulated Time</th>
        <th>Memory Usage</th>
        <th>Memory Peak Usage</th>
      </tr>
    </thead>

    <tbody>
      {assign var="last" value=['time' => $run.startTime, 'memory_usage' => 0]}

      {foreach from=$run.profiler item=row}
        {if $row['block'] == 'checkAccess' && $row['time'] == $last['time']}
          {$continue}
        {/if}

        <tr>
          <td>{$row['block']}</td>
          <td>{load_time data=($row['time'] - $last['time'])}</td>
          <td>{load_time data=($row['time'] - $run.startTime)}</td>
          <td>{memory data=($row['memory_usage'] - $last['memory_usage'])}</td>
          <td>{peak_memory data=($row['peak_memory_usage'])}</td>
        </tr>

        {assign var="last" value=$row}
      {/foreach}
    </tbody>
  </table>
</div>
