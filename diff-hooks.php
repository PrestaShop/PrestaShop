<?php

$path_to_16 = $argv[1];
$path_to_17 = $argv[2];

$grep = "grep -ri --exclude-dir={.git,admin,bin,cache,modules,tests,vendor} --exclude={'diff-hooks.php','diff-hooks.html'}";

exec("$grep 'hook h=' ".$path_to_16."/themes", $hookHIn16);
exec("$grep 'hook h=' ".$path_to_17."/themes", $hookHIn17);

exec("$grep 'Hook::exec' ".$path_to_16, $hookExecIn16);
exec("$grep 'Hook::exec' ".$path_to_17, $hookExecIn17);

$hooks16 = array_merge(getFormattedHookList($hookHIn16, $path_to_16), getFormattedHookList($hookExecIn16, $path_to_16));
$hooks17 = array_merge(getFormattedHookList($hookHIn17, $path_to_17), getFormattedHookList($hookExecIn17, $path_to_17));

ksort($hooks16);
ksort($hooks17);

generateHTML(array_intersect_key($hooks16, $hooks17), array_diff_key($hooks16, $hooks17), array_diff_key($hooks17, $hooks16), $hooks16, $hooks17);

function generateHTML($commonHooks, $hooksOnly16, $hooksOnly17, $hooks16, $hooks17)
{
    $html  =  '
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container">';

    $html .=  '<h1>Common hooks</h1>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Hook name</th><th>File(s) in 1.6</th><th>File(s) in 1.7</th>';
    $html .= '</tr>';
    $html .= '</thead>';

    foreach ($commonHooks as $hookName => $files) {
        $html .= '<tr>';
        $html .= '<td>'.$hookName.'</td><td><ul>';
        foreach ($files as $file) {
            $html .= '<li>'.$file.'</li>';
        }
        $html .= '</ul></td><td><ul>';
        foreach ($hooks17[$hookName] as $file) {
            $html .= '<li>'.$file.'</li>';
        }
        $html .= '</ul></td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    $html .= '<h1>Hooks only in 1.6</h1>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Hook name</th><th>File(s)</th>';
    $html .= '</tr>';
    $html .= '</thead>';

    foreach ($hooksOnly16 as $hookName => $files) {
        $html .= '<tr>';
        $html .= '<td>'.$hookName.'</td><td><ul>';
        foreach ($files as $file) {
            $html .= '<li>'.$file.'</li>';
        }
        $html .= '</ul></td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    $html .= '<h1>Hooks only in 1.7</h1>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Hook name</th><th>File(s)</th>';
    $html .= '</tr>';
    $html .= '</thead>';

    foreach ($hooksOnly17 as $hookName => $files) {
        $html .= '<tr>';
        $html .= '<td>'.$hookName.'</td><td><ul>';
        foreach ($files as $file) {
            $html .= '<li>'.$file.'</li>';
        }
        $html .= '</ul></td>';
        $html .= '</tr>';
    }

    $html .= '</table>';
    $html .= '
    </div>
  </body>
</html>';

    file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'diff-hooks.html', $html);
}

function getFormattedHookList($hookList, $folder)
{
    $list = [];

    foreach ($hookList as $hook) {
        $line = explode(':', $hook, 2);
        if (count($line) !== 2) {
            echo "Warning, could not parse hook in:\n$hook\n\n";
            continue;
        }
        $list[getHookName($line[1])][formatFilePath($line[0], $folder)] = formatFilePath($line[0], $folder);
        ksort($list[getHookName($line[1])]);
    }

    ksort($list);

    return $list;
}

function getHookName($str)
{
    preg_match('/(?:hook h=|Hook::exec\()[\'"](\w+)[\'"]/', $str, $matches);

    return isset($matches[1]) ? $matches[1] : $str;
}

function formatFilePath($path, $folder)
{
    return str_replace($folder, '', $path);
}
