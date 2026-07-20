<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

include '../../../../../config/config.inc.php';

ini_set('max_execution_time', '7200');
ini_set('memory_limit', '512M');

$types = ImageType::getImagesTypes('categories');
$files = scandir(dirname(__FILE__), SCANDIR_SORT_NONE);
foreach ($files as $file) {
    if (preg_match('/^([a-z0-9-_]+)\.jpg$/i', $file, $match) && !preg_match('/default\.jpg$/i', $file)) {
        foreach ($types as $type) {
            if (!file_exists($match[1].'-'.$type['name'].'.jpg')) {
                $error = 0;
                $targetWidth = null;
                $targetHeight = null;
                $sourceWidth = null;
                $sourceHeight = null;

                ImageManager::resize($file, $match[1].'-'.$type['name'].'.jpg', $type['width'], $type['height'], 'jpg', true, $error, $targetWidth, $targetHeight, 5, $sourceWidth, $sourceHeight, $type['image_fitment']);
            }
        }
    }
}
