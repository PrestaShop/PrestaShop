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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Tests\Unit\Core\Security\InMemoryConfiguration;

/**
 * Provides the Symfony firewall configuration array and persistence helpers for login throttling.
 */
class AdminLoginFormThrottlingConfiguration implements DataConfigurationInterface
{
    public const DEFAULT_MAX_ATTEMPTS = 5;
    public const DEFAULT_INTERVAL_MINUTES = 15;
    public const ERROR_KEY_INVALID_CONFIGURATION = 'The admin login throttling configuration is invalid.';
    public const ERROR_KEY_INVALID_STORAGE = 'The selected login throttling storage service is invalid.';

    /**
     * @param Configuration|InMemoryConfiguration $configuration
     */
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly ?ContainerInterface $container = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $maxAttempts = $this->configuration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS', self::DEFAULT_MAX_ATTEMPTS);
        if ($maxAttempts <= 0) {
            $maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
        }
        $interval = (int) $this->configuration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL', self::DEFAULT_INTERVAL_MINUTES);
        if ($interval <= 0) {
            $interval = self::DEFAULT_INTERVAL_MINUTES;
        }

        return [
            'login_throttling_enabled' => (bool) $this->configuration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED', true),
            'login_throttling_max_attempts' => $maxAttempts,
            'login_throttling_interval' => $interval,
            'login_throttling_storage' => (string) $this->configuration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_STORAGE', ''),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $errors = $this->getValidationErrors($configuration);

        if (!empty($errors)) {
            return $errors;
        }

        $this->configuration->set('PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED', (int) (bool) $configuration['login_throttling_enabled']);
        $this->configuration->set('PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS', (int) $configuration['login_throttling_max_attempts']);
        $this->configuration->set('PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL', (int) $configuration['login_throttling_interval']);
        $this->configuration->set('PS_SECURITY_ADMIN_LOGIN_THROTTLING_STORAGE', trim((string) $configuration['login_throttling_storage']));

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration): bool
    {
        return [] === $this->getValidationErrors($configuration);
    }

    public function getFirewallConfiguration(): array|bool
    {
        $configuration = $this->getConfiguration();
        $enabled = (bool) $configuration['login_throttling_enabled'];
        $maxAttempts = (int) $configuration['login_throttling_max_attempts'];
        $intervalMinutes = (int) $configuration['login_throttling_interval'];
        $storageService = (string) $configuration['login_throttling_storage'];

        if (!$enabled) {
            return false;
        }

        $intervalString = $intervalMinutes === 1 ? '1 minute' : sprintf('%d minutes', $intervalMinutes);

        $configuration = [
            'max_attempts' => $maxAttempts,
            'interval' => $intervalString,
        ];

        if ($storageService !== '') {
            $configuration['storage_service'] = $storageService;
        }

        return $configuration;
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<int, array{key: string, parameters: array<string, mixed>, domain: string}>
     */
    private function getValidationErrors(array $configuration): array
    {
        $errors = [];

        if (!isset(
            $configuration['login_throttling_enabled'],
            $configuration['login_throttling_max_attempts'],
            $configuration['login_throttling_interval'],
        )) {
            $errors[] = $this->createError(self::ERROR_KEY_INVALID_CONFIGURATION);

            return $errors;
        }

        if ((int) $configuration['login_throttling_max_attempts'] <= 0) {
            $errors[] = $this->createError(self::ERROR_KEY_INVALID_CONFIGURATION);
        }

        if ((int) $configuration['login_throttling_interval'] <= 0) {
            $errors[] = $this->createError(self::ERROR_KEY_INVALID_CONFIGURATION);
        }

        $storageError = $this->getStorageServiceError($configuration);

        if (null !== $storageError) {
            $errors[] = $storageError;
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function getStorageServiceError(array $configuration): ?array
    {
        if (!isset($configuration['login_throttling_storage'])) {
            return null;
        }

        $storageServiceId = trim((string) $configuration['login_throttling_storage']);

        if ($storageServiceId === '') {
            return null;
        }

        if ($this->container === null) {
            return $this->createError(self::ERROR_KEY_INVALID_STORAGE);
        }

        if (!$this->container->has($storageServiceId)) {
            return $this->createError(self::ERROR_KEY_INVALID_STORAGE);
        }

        $service = $this->container->get($storageServiceId);

        if (!$service instanceof StorageInterface && !$service instanceof CacheItemPoolInterface) {
            return $this->createError(self::ERROR_KEY_INVALID_STORAGE);
        }

        return null;
    }

    private function createError(string $key): array
    {
        return [
            'key' => $key,
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
