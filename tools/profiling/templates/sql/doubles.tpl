$content = '<div class="row">
  <h2><a name="doubles">Doubles</a></h2>
  <table class="table table-condensed">';
    foreach (Db::getInstance()->uniqQueries as $q => $nb) {
    if ($nb > 1) {
    $content .= '<tr><td><span ' . $this->getQueryColor($nb) . '>' . $nb . '</span></td><td class="pre"><pre>' . $q . '</pre></td></tr>';
    }
    }
    $content .= '</table>
</div>';
return $content;
