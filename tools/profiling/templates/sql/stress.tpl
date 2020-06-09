$content = '<div class="row">
  <h2><a name="tables">Tables stress</a></h2>
  <table class="table table-condensed">';
    foreach (Db::getInstance()->tables as $table => $nb) {
    $content .= '<tr><td><span ' . $this->getTableColor($nb) . '>' . $nb . '</span> ' . $table . '</td></tr>';
    }
    $content .= '</table>
</div>';
