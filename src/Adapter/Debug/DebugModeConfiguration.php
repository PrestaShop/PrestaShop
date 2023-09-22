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

namespace PrestaShop\PrestaShop\Adapter\Debug;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\ClassIndexCacheClearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Debug mode configuration for a Shop.
 */
class DebugModeConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var DebugMode Debug mode manager
     */
    private $debugMode;

    /**
     * @var string Path to the application defines path
     */
    private $configDefinesPath;

    /**
     * @var ClassIndexCacheClearer
     */
    private $classIndexCacheClearer;

    /**
     * @var DebugProfiling Debug profiling manager
     */
    private $debugProfiling;

    /**
     * @param DebugMode $debugMode
     * @param Configuration $configuration
     * @param string $configDefinesPath
     * @param ClassIndexCacheClearer $classIndexCacheClearer
     * @param DebugProfiling $debugProfiling
     */
    public function __construct(
        DebugMode $debugMode,
        Configuration $configuration,
        $configDefinesPath,
        ClassIndexCacheClearer $classIndexCacheClearer,
        DebugProfiling $debugProfiling
    ) {
        $this->debugMode = $debugMode;
        $this->configuration = $configuration;
        $this->configDefinesPath = $configDefinesPath;
        $this->classIndexCacheClearer = $classIndexCacheClearer;
        $this->debugProfiling = $debugProfiling;
    }

    /**
     * Returns configuration used to manage Debug mode in back office.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'disable_overrides' => $this->configuration->getBoolean('PS_DISABLE_OVERRIDES'),
            'debug_mode' => $this->debugMode->isDebugModeEnabled(),
            'debug_cookie_name' => $this->configuration->get('PS_DEBUG_COOKIE_NAME'),
            'debug_cookie_value' => $this->configuration->get('PS_DEBUG_COOKIE_VALUE'),
            'debug_profiling' => $this->debugProfiling->isProfilingEnabled(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            // Set configuration
            $this->configuration->set('PS_DISABLE_OVERRIDES', $configuration['disable_overrides']);

            $this->configuration->set('PS_DEBUG_COOKIE_NAME', $configuration['debug_cookie_name']);
            $this->configuration->set('PS_DEBUG_COOKIE_VALUE', $configuration['debug_cookie_value']);

            if (empty($configuration['debug_cookie_name']) && !empty($configuration['debug_cookie_value'])) {
                $errors[] = [
                    'key' => 'Error: The cookie name is required when the cookie value is set.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ];
            }

            $this->classIndexCacheClearer->clear();

            // Update Debug Mode
            $status = $this->updateDebugMode($configuration);
            switch ($status) {
                case DebugMode::DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM:
                case DebugMode::DEBUG_MODE_ERROR_NO_READ_ACCESS:
                case DebugMode::DEBUG_MODE_ERROR_NO_WRITE_ACCESS:
                    $errors[] = [
                        'key' => 'Error: Could not write to file. Make sure that the correct permissions are set on the file %s',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => [$this->configDefinesPath],
                    ];

                    break;
                case DebugMode::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND:
                    $errors[] = [
                        'key' => 'Error: Could not find whether debug mode is enabled. Make sure that the correct permissions are set on the file %s',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => [$this->configDefinesPath],
                    ];

                    break;
                case DebugMode::DEBUG_MODE_SUCCEEDED:
                default:
                    break;
            }

            // Update Debug Profiler
            $status = $this->updateDebugProfiling((bool) $configuration['debug_profiling']);
            switch ($status) {
                case DebugProfiling::DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS_CUSTOM:
                case DebugProfiling::DEBUG_PROFILING_ERROR_NO_READ_ACCESS:
                case DebugProfiling::DEBUG_PROFILING_ERROR_NO_WRITE_ACCESS:
                    $errors[] = [
                        'key' => 'Error: Could not write to file. Make sure that the correct permissions are set on the file %s',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => [$this->configDefinesPath],
                    ];

                    break;
                case DebugProfiling::DEBUG_PROFILING_ERROR_NO_DEFINITION_FOUND:
                    $errors[] = [
                        'key' => 'Error: Could not find whether debug profiling is enabled. Make sure that the correct permissions are set on the file %s',
                        'domain' => 'Admin.Advparameters.Notification',
                        'parameters' => [$this->configDefinesPath],
                    ];

                    break;
                case DebugProfiling::DEBUG_PROFILING_SUCCEEDED:
                default:
                    break;
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $keys = [
            'disable_overrides',
            'debug_mode',
            'debug_cookie_name',
            'debug_cookie_value',
            'debug_profiling',
        ];
        $array_keys = array_keys($configuration);

        return count(array_intersect($keys, $array_keys)) === count($keys);
    }

    /**
     * Change Debug mode value if needed.
     *
     * @param array $configuration {
     *                             debug_mode: bool
     *                             debug_cookie_name: string
     *                             debug_cookie_value: string
     *                             }
     *
     * @return int|null Status of update
     */
    private function updateDebugMode(array $configuration): ?int
    {
        $currentDebugMode = $this->debugMode->getCurrentDebugMode();

        $newDebugMode = $this->debugMode->createDebugModeFromConfiguration($configuration);

        if ($newDebugMode !== $currentDebugMode) {
            return $this->debugMode->changePsModeDevValue($newDebugMode);
        }

        return null;
    }

    /**
     * Change Debug profiling value if needed.
     *
     * @param bool $enableStatus
     *
     * @return int|null Status of update
     */
    private function updateDebugProfiling(bool $enableStatus): ?int
    {
        $isProfilingEnabled = $this->debugProfiling->isProfilingEnabled();

        if ($enableStatus !== $isProfilingEnabled) {
            return (true === $enableStatus) ? $this->debugProfiling->enable() : $this->debugProfiling->disable();
        }

        return null;
    }
}
