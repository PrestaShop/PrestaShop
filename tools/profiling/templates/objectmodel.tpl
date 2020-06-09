<div class="row">
  <h2>
    <a name="objectModels">
      ObjectModel instances
    </a>
  </h2>

  <table class="table table-condensed">
    <tr>
      <th>Name</th>
      <th>Instances</th>
      <th>Source</th>
    </tr>
    foreach (ObjectModel::$debug_list as $class => $info) {
    $content .= '<tr>
      <td>' . $class . '</td>
      <td><span ' . $this->getObjectModelColor(count($info)) . '>' . count($info) . '</span></td>
      <td>';
        foreach ($info as $trace) {
        $content .= str_replace([_PS_ROOT_DIR_, '\\'], ['', '/'], $trace['file']) . ' [' . $trace['line'] . ']<br />';
        }
        $content .= '    </td>
    </tr>';
    }
  </table>
</div>
