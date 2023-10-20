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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Debug;

use Tools;

/**
 * Utilitary class to manages the Debug profiling legacy application.
 */
class DebugProfiling
{
    public const DEBUG_PROFILING_SUCCEEDED = 0;
    public const DEBUG_PROFILING_ERROR_NO_READ_ACCESS = 1;
    public const DEBUG_PROFILING_ERROR_NO_READ_ACCESS_CUSTOM = 2;
    public const DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS = 3;
    public const DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS_CUSTOM = 4;
    public const DEBUG_PROFILING_ERROR_NO_DEFINITION_FOUND = 5;

    /**
     * Is the profiler enabled? Checks on custom defines file first.
     *
     * @return bool Whether debug profiling is enabled
     */
    public function isProfilingEnabled(): bool
    {
        $definesClean = '';
        $customDefinesPath = _PS_ROOT_DIR_ . '/config/defines_custom.inc.php';
        $definesPath = _PS_ROOT_DIR_ . '/config/defines.inc.php';

        if (is_readable($customDefinesPath)) {
            $definesClean = php_strip_whitespace($customDefinesPath);
        }

        if (!preg_match('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', $definesClean, $debugProfilingValue)) {
            $definesClean = php_strip_whitespace($definesPath);
            if (!preg_match('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', $definesClean, $debugProfilingValue)) {
                return false;
            }
        }

        return 'true' === Tools::strtolower($debugProfilingValue[1]);
    }

    /**
     * Enable Debug profiling.
     *
     * @return int Whether changing debug profiling succeeded or error code
     */
    public function enable(): int
    {
        return $this->changeProfilingValue('true');
    }

    /**
     * Disable debug profiling.
     *
     * @return int Whether changing debug profiling succeeded or error code
     */
    public function disable(): int
    {
        return $this->changeProfilingValue('false');
    }

    /**
     * Check read permission on custom defines.inc.php.
     *
     * @return bool Whether the file can be read
     */
    private function isCustomDefinesReadable(): bool
    {
        return is_readable(_PS_ROOT_DIR_ . '/config/defines_custom.inc.php');
    }

    /**
     * Check read permission on main defines.inc.php.
     *
     * @return bool Whether the file can be read
     */
    private function isMainDefinesReadable(): bool
    {
        return is_readable(_PS_ROOT_DIR_ . '/config/defines.inc.php');
    }

    /**
     * Update Debug profiling value in main defines file.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int the debug profiling
     */
    private function updateProfilingValueInMainFile(string $value): int
    {
        $filename = _PS_ROOT_DIR_ . '/config/defines.inc.php';
        $cleanedFileContent = php_strip_whitespace($filename);
        $fileContent = Tools::file_get_contents($filename);

        if (!preg_match('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', $cleanedFileContent)) {
            return self::DEBUG_PROFILING_ERROR_NO_DEFINITION_FOUND;
        }

        $fileContent = preg_replace('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_DEBUG_PROFILING_\', ' . $value . ');', $fileContent);
        if (!@file_put_contents($filename, $fileContent)) {
            return self::DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($filename);
        }

        return self::DEBUG_PROFILING_SUCCEEDED;
    }

    /**
     * Update Debug profiling value in custom defines file.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int Debug profiling
     */
    private function updateProfilingValueInCustomFile(string $value): int
    {
        $customFileName = _PS_ROOT_DIR_ . '/config/defines_custom.inc.php';
        $cleanedFileContent = php_strip_whitespace($customFileName);
        $fileContent = Tools::file_get_contents($customFileName);

        if (!preg_match('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', $cleanedFileContent)) {
            return self::DEBUG_PROFILING_ERROR_NO_DEFINITION_FOUND;
        }
        $fileContent = preg_replace('/define\(\'_PS_DEBUG_PROFILING_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_DEBUG_PROFILING_\', ' . $value . ');', $fileContent);

        if (!@file_put_contents($customFileName, $fileContent)) {
            return self::DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS_CUSTOM;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($customFileName);
        }

        return self::DEBUG_PROFILING_SUCCEEDED;
    }

    /**
     * Change value of _PS_DEBUG_PROFILING_ constant.
     *
     * @param string $value should be "true" or "false"
     *
     * @return int the debug profiling
     */
    private function changeProfilingValue(string $value): int
    {
        // Check custom defines file first
        if ($this->isCustomDefinesReadable()) {
            return $this->updateProfilingValueInCustomFile($value);
        }

        if ($this->isMainDefinesReadable()) {
            return $this->updateProfilingValueInMainFile($value);
        }

        return self::DEBUG_PROFILING_ERROR_NO_READ_ACCESS;
    }
}
