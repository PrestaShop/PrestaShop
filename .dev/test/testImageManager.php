<?php
require dirname(__FILE__) . '/../../config/config.inc.php';
Shop::setContext(Shop::CONTEXT_ALL);

$id_image = 220;
$extensions = ['jpg', 'png', 'gif', 'webp'];
$settings = [
    ['PS_IMAGE_QUALITY' => 'png', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png', 'PS_IMAGE_GENERATION_METHOD' => 1, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png', 'PS_IMAGE_GENERATION_METHOD' => 2, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png', 'PS_IMAGE_GENERATION_METHOD' => 1, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png', 'PS_IMAGE_GENERATION_METHOD' => 2, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png_all', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'webp', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'webp_fb', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 0, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png_all', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 0, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png_all', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 1, 'PS_IMAGEOPT_PNGQUANT' => 0, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png_all', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 1, 'PS_IMAGEOPT_PNGQUANT' => 1, 'PS_IMAGEOPT_OPTIPNG' => 0],
    ['PS_IMAGE_QUALITY' => 'png_all', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 1, 'PS_IMAGEOPT_PNGQUANT' => 1, 'PS_IMAGEOPT_OPTIPNG' => 1],
    ['PS_IMAGE_QUALITY' => 'webp_fb', 'PS_IMAGE_GENERATION_METHOD' => 0, 'PS_IMAGEOPT_NO_ENLARGE' => 1, 'PS_IMAGEOPT_SYMLINK' => 1, 'PS_IMAGEOPT_PNGQUANT' => 1, 'PS_IMAGEOPT_OPTIPNG' => 1],
];
echo '<html><head></head><style>
img {max-width: 200px; background-color: #eee;border: 1px #666 dotted;}
img:hover {background-color: #ddd;}
.infos {white-space: nowrap}
table {border-collapse: collapse;}
.first td {border-top: 2px solid black;}
</style><body><table>';
echo '<tr><td>Source images</td>';
foreach ($extensions as $extension) {
    $image = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'testImageManager.' . $extension;
    echo '<td><img src="./fixtures/testImageManager.' . $extension . '"></td>';
    echo '<td class="infos">';
    foreach (getInfo($image) as $name => $value) {
        echo '<br>' . ucfirst($name) . ': '. $value;
    }
    echo '</td>';
}
echo '</tr>';
$path_abs = _PS_PROD_IMG_DIR_;
$path_rel = '/img/p/';
$image_types = ImageType::getImagesTypes('products', true);
foreach ($settings as $setting) {
    foreach ($setting as $name => $value) {
        Configuration::updateValue($name, $value);
    }
    $first_line = true;
    foreach ($extensions as $extension){
        echo '<tr' . ($first_line ? ' class= "first" ' : '') . '><td>';
        $first_line = false;
        echo 'Extension: '. $extension;
        foreach ($setting as $name => $value) {
            echo  '<br>' . $name . ': '. $value;
        }
        echo '</td>';
        $imageFolder = Image::getImgFolderStatic($id_image);
        $files = glob($path_abs . $imageFolder . '*');
        foreach($files as $file){
            if(is_file($file) || is_link($file)) {
                unlink($file);
            }
        }
        chmod($path_abs . $imageFolder, 0775);
        copy(
            __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'testImageManager.' . $extension,
            $path_abs . $imageFolder . $id_image . '.jpg'
        );
        foreach ($image_types as $image_type) {
            $thumb = $path_abs . $imageFolder . $id_image . '-' . $image_type['name'] . '.jpg';
            ImageManager::resize(
                $path_abs . $imageFolder . $id_image . '.jpg',
                $thumb,
                (int) $image_type['width'],
                (int) $image_type['height']
            );
            echo '<td><img src="' . $path_rel . $imageFolder .  $id_image . '-' . $image_type['name'] . '.jpg' . '"></td>';
            echo '<td class="infos">' . 'Name: ' . $id_image . '-' . $image_type['name'] . '.jpg';
            foreach (getInfo($thumb) as $name => $value) {
                echo '<br>' . ucfirst($name) . ': '. $value;
            }
            echo '</td>';

        }
        echo '</tr>';
        if($setting['PS_IMAGE_QUALITY'] == 'webp_fb'){
            echo '<tr><td>Duplicates in webp format</td>';
            foreach ($image_types as $image_type) {
                $thumb = $path_abs . $imageFolder . $id_image . '-' . $image_type['name'] . '.webp';
                echo '<td><img src="' . $path_rel . $imageFolder . $id_image . '-' . $image_type['name'] . '.webp' . '"></td>';
                echo '<td class="infos">' . 'Name: ' . $id_image . '-' . $image_type['name'] . '.webp';
                foreach (getInfo($thumb) as $name => $value) {
                    echo '<br>' . ucfirst($name) . ': ' . $value;
                }
                echo '</td>';
            }
            echo '</tr>';
        }
        $id_image++;
    }
}
echo '</table></body>';

function getInfo($image) {
    $size = getimagesize($image);
    $infos = [];
    $infos['width'] = $size[0];
    $infos['height'] = $size[1];
    $infos['mime'] = $size['mime'];
    $infos['bytes'] = filesize($image);
    if(is_link($image)) {
        $infos['symlink'] = 'true';
    }
    return $infos;
}
