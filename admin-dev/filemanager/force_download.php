<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

include 'config/config.php';

if ($_SESSION['verify'] != 'RESPONSIVEfilemanager') {
    die('Forbidden');
}
include 'include/utils.php';

if (preg_match('/\.{1,2}[\/|\\\]/', $_POST['path']) !== 0) {
    die('wrong path');
}

if (strpos($_POST['name'], '/') !== false || strpos($_POST['name'], '\\') !== false) {
    die('wrong path');
}

$path = $current_path.$_POST['path'];
$name = $_POST['name'];

$info = pathinfo($name);
if (!in_array(mb_strtolower($info['extension']), $ext)) {
    die('wrong extension');
}

header('Pragma: private');
header('Cache-control: private, must-revalidate');
header('Content-Type: application/octet-stream');
header('Content-Length: '.(string)filesize($path.$name));
header('Content-Disposition: attachment; filename="'.($name).'"');
readfile($path.$name);

exit;
