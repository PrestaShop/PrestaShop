$content = '
<div class="row">
  <h2><a name="stopwatch">Stopwatch SQL - ' . count($this->array_queries) . ' queries</a></h2>
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
    <tbody>';
      foreach ($this->array_queries as $data) {
      $callstack = implode('<br>', $data['stack']);
      $callstack_md5 = md5($callstack);

      $content .= '
      <tr>
        <td class="pre"><pre>' . preg_replace("/(^[\s]*)/m", '', htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false)) . '</pre></td>
        <td data-value="' . $data['time'] . '"><span ' . $this->getTimeColor($data['time'] * 1000) . '>' . (round($data['time'] * 1000, 1) < 0.1 ? '< 1' : round($data['time'] * 1000, 1)) . '</span></td>
        <td>' . (int) $data['rows'] . '</td>
        <td data-value="' . $data['filesort'] . '">' . ($data['filesort'] ? '<span style="color:' . static::COLOR_ERROR . '">Yes</span>' : '') . '</td>
        <td data-value="' . $data['group_by'] . '">' . ($data['group_by'] ? '<span style="color:' . static::COLOR_ERROR . '">Yes</span>' : '') . '</td>
        <td data-value="' . $data['location'] . '">
          <a href="javascript:void(0);" onclick="$(\'#callstack_' . $callstack_md5 . '\').toggle();">' . $data['location'] . '</a>
          <div id="callstack_' . $callstack_md5 . '" style="display:none">' . implode('<br>', $data['stack']) . '</div>
        </td>
      </tr>';
      }
      $content .= '</table>
</div>';

return $content;
