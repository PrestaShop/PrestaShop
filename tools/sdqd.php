<?php
header('Content-type: text/html; charset=utf-8');

include('../config/config.inc.php');
include('../init.php');

ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
ini_set("realpath_cache_size", '5M');
ini_set('max_execution_time', 60000);
error_reporting(E_ALL);

$expr = "capteur capterr resea humida th zzzzzzzzzzzz xxxxxx xxxxxxxxxxxxxxx lllll hhhhhhhhhhhhhhhhhhhhhh";

$expr = "atmosphere accessories rendering project sleeves eeeeeeee eeeeeeeeeeeeee eeeeeeeeeeeeeeeeeeeee";

$timeStart = microtime_float();
$result = Search::find(
    1,
    $expr,
    1,
    10,
    'position',
    'desc',
    false, // ajax, what's the link?
    false, // $use_cookie, ignored anSearch for close wordyway
    null
);
$timeEnd   = microtime_float();

dump($result);
dump($timeEnd - $timeStart);

