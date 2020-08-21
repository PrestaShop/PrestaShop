<div class="row">
  <h2>
    <a name="stopwatch">
      Stopwatch SQL - {$summary.nbQueries} queries
    </a>
  </h2>

  <table class="table table-condensed table-bordered sortable">
    <thead>
      <tr>
        <th>Query</th>
        <th>Time (ms)</th>
        <th>Rows</th>
        <th>Filesort</th>
        <th>Group By</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
      {foreach $stopwatchQueries as $data}
        {$callstack = implode('<br>', $data['stack'])}
        {$callstack_md5 = md5($callstack)}
        <tr>
          <td class="pre"><pre>{preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false))}</pre></td>
          <td data-value="{$data['time']}">
            {load_time data=($data['time'] * 1000)}
          </td>

          <td>{$data['rows']}</td>
          <td data-value="{$data['filesort']}">
            {if $data['filesort']}
              <span class="danger">Yes</span>
            {/if}
          </td>
          <td data-value="{$data['group_by']}">
            {if $data['group_by']}
              <span class="danger"">Yes</span>
            {/if}
          </td>
          <td data-value="{$data['location']}">
            <a href="javascript:void(0);" onclick="$('#callstack_{$callstack_md5}').toggle();">{$data['location']}</a>
            <div id="callstack_' . $callstack_md5}" style="display:none">{implode('<br>', $data['stack'])}</div>
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>
