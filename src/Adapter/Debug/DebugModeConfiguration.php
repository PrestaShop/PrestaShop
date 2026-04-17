<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Debug;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\ClassIndexCacheClearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Debug mode configuration for a Shop.
 */
class DebugModeConfiguration implements DataConfigurationInterface
{
    /**
     * @param DebugMode $debugMode Debug mode manager
     * @param Configuration $configuration
     * @param string $configDefinesPath Path to the application defines path
     * @param ClassIndexCacheClearer $classIndexCacheClearer
     * @param DebugProfiling $debugProfiling Debug profiling manager
     * @param CacheClearerInterface $cacheClearer
     */
    public function __construct(
        private DebugMode $debugMode,
        private Configuration $configuration,
        private string $configDefinesPath,
        private ClassIndexCacheClearer $classIndexCacheClearer,
        private DebugProfiling $debugProfiling,
        private CacheClearerInterface $cacheClearer,
    ) {
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
            $updated = $this->debugMode->changePsModeDevValue($newDebugMode);
            $this->cacheClearer->clear();

            return $updated;
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
