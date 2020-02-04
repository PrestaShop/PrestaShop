<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Hosting;

use Db;
use Tools;

/**
 * Provides hosting system information.
 */
class HostingInformation
{
    /**
     * @return array
     */
    public function getDatabaseInformation()
    {
        return [
            'version' => Db::getInstance()->getVersion(),
            'server' => _DB_SERVER_,
            'name' => _DB_NAME_,
            'user' => _DB_USER_,
            'prefix' => _DB_PREFIX_,
            'engine' => _MYSQL_ENGINE_,
            'driver' => Db::getClass(),
        ];
    }

    /**
     * @return array
     */
    public function getServerInformation()
    {
        return [
            'version' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'n/a',
            'php' => $this->getPhpInformation(),
        ];
    }

    /**
     * @return array
     */
    private function getPhpInformation()
    {
        return [
            'version' => PHP_VERSION,
            'memoryLimit' => ini_get('memory_limit'),
            'maxExecutionTime' => ini_get('max_execution_time'),
            'maxFileSizeUpload' => ini_get('upload_max_filesize'),
        ];
    }

    /**
     * @return string
     */
    public function getUname()
    {
        return function_exists('php_uname') ? php_uname('s') . ' ' . php_uname('v') . ' ' . php_uname('m') : '';
    }

    /**
     * @return bool
     */
    public function isApacheInstawebModule()
    {
        return Tools::apacheModExists('mod_instaweb');
    }

    /**
     * Check if the shop is hosted on PrestaCloud.
     *
     * @return bool
     */
    public function isHostMode()
    {
        return defined('_PS_HOST_MODE_');
    }
}
