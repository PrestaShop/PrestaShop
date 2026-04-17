<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
spl_autoload_register(function ($className) {
    if (0 === strpos($className, 'InstallControllerConsole')) {
        $fileName = strtolower(str_replace('InstallControllerConsole', '', $className));
        require_once sprintf('%s/controllers/console/%s.php', __DIR__, $fileName);
    } else if (0 === strpos($className, 'InstallControllerHttp')) {
        $fileName = strtolower(str_replace('InstallControllerHttp', '', $className));
        require_once sprintf('%s/controllers/http/%s.php', __DIR__, $fileName);
    } else if (file_exists(__DIR__ . '/classes/' . $className . '.php')) {
        require_once sprintf('%s/classes/%s.php', __DIR__, $className);
    }
});
