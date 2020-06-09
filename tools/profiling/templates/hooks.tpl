<div class="col-4">
  <table class="table table-condensed">
    <tr>
      <th>Hook</th>
      <th>Time</th>
      <th>Memory Usage</th>
    </tr>

    {foreach $hooks.perfs as $hook => $hooksPerfs}
      <tr>
        <td>
          <a href="javascript:void(0);" onclick="$('.{$hook}_modules_details').toggle();">{$hook}</a>
        </td>
        <td>
          {load_time data=$hooksPerfs['time']} ms
        </td>
        <td>
          {memory data=$hooksPerfs['memory']} Mb
        </td>
      </tr>

      {foreach $hooksPerfs['modules'] as $perfs}
        <tr class="{$hook}_modules_details" style="background-color:#EFEFEF;display:none">
          <td>
            =&gt; {$perfs['module']}
          </td>
          <td>
            {load_time data=$perfs['time']} ms
          </td>
          <td>
            {memory data=$perfs['memory']} Mb
          </td>
        </tr>
      {/foreach}
    {/foreach}

    <tr>
      <th><b>{$hooks.perfs|count} hook(s)</b></th>
      <th>{load_time data=$hooks.totalModulesTime} ms</th>
      <th>{memory data=$hooks.totalModulesMemory} Mb</th>
    </tr>
  </table>
</div>
