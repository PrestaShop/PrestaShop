<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return string
     */
    public function getHostname(): string
    {
        return gethostname();
    }
}
