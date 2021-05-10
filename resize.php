<?php
require(dirname(__FILE__).'/config/config.inc.php');
$url = filter_input(INPUT_GET, 'url');

preg_match('/^(img\/([^\/]*)\/(?:\d\/)*)(\d+)-([_a-z0-9]+)\.([a-z]+)$/', $url, $matches);
$url_path = $matches[1];
$img_path = $matches[2];
$id_image = $matches[3];
$size_name = $matches[4];
$ext = $matches[5];
switch ($img_path) {
    case 'p':
        $type = 'products';
        break;
    case 'c':
        $type = 'categories';
        break;
    case 'm':
        $type = 'manufacturers';
        break;
    case 'su':
        $type = 'suppliers';
        break;
    case 'st':
        $type = 'stores';
        break;
}
if (!isset($type)) {
    http_response_code(400);
    die();
}
$image_types = ImageType::getImagesTypes($type, true);
foreach ($image_types as $item) {
    if ($item['name'] == $size_name) {
        $image_type = $item;
        break;
    }
}
if (!isset($image_types)) {
    http_response_code(400);
    die();
}
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$full_path = realpath($doc_root).'/'.$url_path;
$file_resized = "$full_path$id_image-$size_name.$ext";
if (file_exists($file_resized)) {
    display($file_resized);
} else {
    $file_fullsize = "$full_path$id_image.$ext";
    if (file_exists($file_fullsize)) {
        display($file_fullsize);
        ImageManager::resize($file_fullsize, $file_resized, $image_type['width'], $image_type['height']);
    } else {
        http_response_code(400);
        die();
    }
}

function display ($filename){
    if(!file_exists($filename)){
        $filename = _PS_IMG_DIR_.'404.gif';
    }
    ignore_user_abort(true);
    $mime = mime_content_type($filename);
    $size = filesize($filename);
    header("Connection: close");
    header("Content-Length: $size");
    header("Content-type: $mime");
    readfile($filename);
}
