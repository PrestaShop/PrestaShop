<?php

$template = file_get_contents(__DIR__.'/index_template.php');

if ($handle = opendir(__DIR__.'/content')) {
  while (false !== ($entry = readdir($handle))) {
    $filePath = __DIR__.'/content/'.$entry;
    if (is_file($filePath)) {
      echo "File found: $entry\n";
      if (strpos($template, $entry)) {
        echo "\033[0;32mReplace entry: $entry\033[0m\n";
        $template = str_replace(
          "'$entry'",
          '<<<EOF'.PHP_EOL.base64_encode(file_get_contents($filePath)).PHP_EOL.'EOF'.PHP_EOL,
          $template
        );
      } else {
        echo "\033[0;31mFile not present on the template\033[0m\n";
      }
    }
  }
}

file_put_contents(getcwd().'/index.php', $template);
