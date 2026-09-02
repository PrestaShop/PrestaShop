{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="row">
  <h2>
    <a name="stopwatch">
      Stopwatch SQL - {$summary.nbQueries} queries
    </a>
  </h2>

  <table class="table table-condensed table-bordered sortable">
    <thead>
      <tr>
        <th>#</th>
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
          <td>{$data['id']}</td>
          <td class="pre" style="max-width: 60vw"><pre>{preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false))}</pre></td>
          <td data-value="{sprintf('%01.6f', $data['time'])}">
            {load_time data=($data['time'])}
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
            <div id="callstack_{$callstack_md5}" style="display:none">
              {foreach $data['stack'] as $stack}
                {$stack}<br/>
              {/foreach}
            </div>
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>
