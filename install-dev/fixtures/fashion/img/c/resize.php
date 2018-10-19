<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


include('../../../../../config/config.inc.php');
ini_set('max_execution_time', 7200);
ini_set('memory_limit', '512M');

$types = ImageType::getImagesTypes('categories');
$files = scandir(dirname(__FILE__), SCANDIR_SORT_NONE);
foreach ($files as $file) {
    if (preg_match('/^([a-z0-9-_]+)\.jpg$/i', $file, $match) && !preg_match('/default\.jpg$/i', $file)) {
        foreach ($types as $type) {
            if (!file_exists($match[1].'-'.$type['name'].'.jpg')) {
                ImageManager::resize($file, $match[1].'-'.$type['name'].'.jpg', $type['width'], $type['height'], 'jpg', true);
            }
        }
    }
}
