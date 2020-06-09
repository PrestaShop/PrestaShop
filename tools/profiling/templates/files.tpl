<div class="row">
  <h2>
    <a name="includedFiles">
      Included Files
    </a>
  </h2>

  <table class="table table-condensed">
    <tr>
      <th>#</th>
      <th>Filename</th>
    </tr>
    foreach (get_included_files() as $file) {
    $file = str_replace('\\', '/', str_replace(_PS_ROOT_DIR_, '', $file));
    if (strpos($file, '/tools/profiling/') === 0) {
    continue;
    }
    $content .= '<tr><td>' . (++$i) . '</td><td>' . $file . '</td></tr>';
    }
    </table>
</div>
