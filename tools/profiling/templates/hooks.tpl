<div class="col-4">
  <table class="table table-condensed">
    <thead>
      <tr>
        <th>Hook</th>
        <th>Time</th>
        <th>Memory Usage</th>
      </tr>
    </thead>

    <tbody>
    {foreach $hooks.perfs as $hook => $hooksPerfs}
      <tr>
        <td>
          <a href="javascript:void(0);" onclick="$('.{$hook}_modules_details').toggle();">{$hook}</a>
        </td>
        <td>
          {load_time data=$hooksPerfs['time']}
        </td>
        <td>
          {memory data=$hooksPerfs['memory']}
        </td>
      </tr>

      {foreach $hooksPerfs['modules'] as $perfs}
        <tr class="{$hook}_modules_details" style="background-color:#EFEFEF;display:none">
          <td>
            =&gt; {$perfs['module']}
          </td>
          <td>
            {load_time data=$perfs['time']}
          </td>
          <td>
            {memory data=$perfs['memory']}
          </td>
        </tr>
      {/foreach}
    {/foreach}

    </tbody>
    <tfoot>
      <tr>
        <th><b>{$hooks.perfs|count} hook(s)</b></th>
        <th>{load_time data=$hooks.totalHooksTime}</th>
        <th>{memory data=$hooks.totalHooksMemory}</th>
      </tr>
    </tfoot>
  </table>
</div>
