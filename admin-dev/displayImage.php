<?php
/**
 * 2007-2018 PrestaShop.
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
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
require_once _PS_ADMIN_DIR_.'/../config/config.inc.php';
require_once _PS_ADMIN_DIR_.'/init.php';

if (isset($_GET['img']) && Validate::isMd5($_GET['img']) && isset($_GET['name']) && Validate::isGenericName($_GET['name']) && file_exists(_PS_UPLOAD_DIR_.$_GET['img'])) {
    header('Content-type: image/jpeg');
    header('Content-Disposition: attachment; filename="'.$_GET['name'].'.jpg"');
    echo file_get_contents(_PS_UPLOAD_DIR_.$_GET['img']);
}
