<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Debug;

use Tools;

/**
 * Utilitary class to manages the Debug mode legacy application.
 */
class DebugMode
{
    public const DEBUG_MODE_SUCCEEDED = 0;
    public const DEBUG_MODE_ERROR_NO_READ_ACCESS = 1;
    public const DEBUG_MODE_ERROR_NO_READ_ACCESS_CUSTOM = 2;
    public const DEBUG_MODE_ERROR_NO_WRITE_ACCESS = 3;
    public const DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM = 4;
    public const DEBUG_MODE_ERROR_NO_DEFINITION_FOUND = 5;

    /**
     * Is Debug Mode enabled? Checks on custom defines file first.
     *
     * @return bool Whether debug mode is enabled
     */
    public function isDebugModeEnabled()
    {
        return 'false' !== Tools::strtolower($this->getCurrentDebugMode());
    }

    /**
     * Get the current debug mode from the defines file.
     *
     * @return string|null
     */
    public function getCurrentDebugMode()
    {
        $definesClean = '';
        $customDefinesPath = _PS_ROOT_DIR_ . '/config/defines_custom.inc.php';
        $definesPath = _PS_ROOT_DIR_ . '/config/defines.inc.php';

        if (is_readable($customDefinesPath)) {
            $definesClean = php_strip_whitespace($customDefinesPath);
        }

        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', $definesClean, $debugModeValue)) {
            $definesClean = php_strip_whitespace($definesPath);
            if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', $definesClean, $debugModeValue)) {
                return null;
            }
        }

        return $debugModeValue[1];
    }

    /**
     * Create php code based on the debug mode configuration.
     * Examples:
     *  define('_PS_MODE_DEV_', true);
     *  define('_PS_MODE_DEV_', isset($_COOKIE['debug']) && $_COOKIE['debug'] === 'debug_password');
     *  define('_PS_MODE_DEV_', isset($_COOKIE['debug']));
     *  define('_PS_MODE_DEV_', false);
     *
     * @param array $configuration {
     *                             debug_mode: bool
     *                             debug_cookie_name: string
     *                             debug_cookie_value: string
     *                             }
     *
     * @return string
     */
    public function createDebugModeFromConfiguration(array $configuration)
    {
        if (!$configuration['debug_mode']) {
            return 'false';
        }

        if (empty($configuration['debug_cookie_name'])) {
            return 'true';
        }

        $debug_cookie_name = stripslashes($configuration['debug_cookie_name']);

        if (empty($configuration['debug_cookie_value'])) {
            return "isset(\$_COOKIE['$debug_cookie_name'])";
        }

        $debug_cookie_value = stripslashes($configuration['debug_cookie_value']);

        return "isset(\$_COOKIE['$debug_cookie_name']) && \$_COOKIE['$debug_cookie_name'] === '$debug_cookie_value'";
    }

    /**
     * Enable Debug mode.
     *
     * @return int Whether changing debug mode succeeded or error code
     */
    public function enable()
    {
        return $this->changePsModeDevValue('true');
    }

    /**
     * Disable debug mode.
     *
     * @return int Whether changing debug mode succeeded or error code
     */
    public function disable()
    {
        return $this->changePsModeDevValue('false');
    }

    /**
     * Check read permission on custom defines.inc.php.
     *
     * @return bool Whether the file can be read
     */
    private function isCustomDefinesReadable()
    {
        return is_readable(_PS_ROOT_DIR_ . '/config/defines_custom.inc.php');
    }

    /**
     * Check read permission on main defines.inc.php.
     *
     * @return bool Whether the file can be read
     */
    private function isMainDefinesReadable()
    {
        return is_readable(_PS_ROOT_DIR_ . '/config/defines.inc.php');
    }

    /**
     * Update Debug Mode value in main defines file.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int the debug mode
     */
    private function updateDebugModeValueInMainFile($value)
    {
        $filename = _PS_ROOT_DIR_ . '/config/defines.inc.php';
        $cleanedFileContent = php_strip_whitespace($filename);
        $fileContent = Tools::file_get_contents($filename);

        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', $cleanedFileContent)) {
            return self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND;
        }

        $fileContent = preg_replace('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', ' . $value . ');', $fileContent);
        if (!@file_put_contents($filename, $fileContent)) {
            return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS;
        }

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($filename);
        }

        return self::DEBUG_MODE_SUCCEEDED;
    }

    /**
     * Update Debug Mode value in custom defines file.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int Debug mode
     */
    private function updateDebugModeValueInCustomFile($value)
    {
        $customFileName = _PS_ROOT_DIR_ . '/config/defines_custom.inc.php';
        $cleanedFileContent = php_strip_whitespace($customFileName);
        $fileContent = Tools::file_get_contents($customFileName);

        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', $cleanedFileContent)) {
            return self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND;
        }
        $fileContent = preg_replace('/define\(\'_PS_MODE_DEV_\', ([^;]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', ' . $value . ');', $fileContent);

        if (!@file_put_contents($customFileName, $fileContent)) {
            return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM;
        }

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($customFileName);
        }

        return self::DEBUG_MODE_SUCCEEDED;
    }

    /**
     * Change value of _PS_MODE_DEV_ constant.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int the debug mode
     */
    public function changePsModeDevValue($value)
    {
        // Check custom defines file first
        if ($this->isCustomDefinesReadable()) {
            $result = $this->updateDebugModeValueInCustomFile($value);
            // If the constant is not found in custom file, fallback to main file
            if ($result !== self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND) {
                return $result;
            }
        }

        if ($this->isMainDefinesReadable()) {
            return $this->updateDebugModeValueInMainFile($value);
        }

        return self::DEBUG_MODE_ERROR_NO_READ_ACCESS;
    }
}
