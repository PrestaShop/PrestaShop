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

namespace Tests\Unit\Core\Security;

use DateInterval;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Security\AdminLoginFormThrottlingConfiguration;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\RateLimiter\LimiterStateInterface;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use UnitEnum;

class AdminLoginFormThrottlingConfigurationTest extends TestCase
{
    public function testConfigurationDefaults(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED' => true,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS' => 5,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL' => 15,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_STORAGE' => '',
        ]));

        $this->assertSame(
            [
                'login_throttling_enabled' => true,
                'login_throttling_max_attempts' => 5,
                'login_throttling_interval' => 15,
                'login_throttling_storage' => '',
            ],
            $configuration->getConfiguration()
        );
    }

    public function testUpdateConfigurationPersistsValues(): void
    {
        $inMemoryConfiguration = new InMemoryConfiguration([]);
        $configuration = new AdminLoginFormThrottlingConfiguration(
            $inMemoryConfiguration,
            $this->createContainerWithCacheService('cache.system')
        );

        $this->assertSame([], $configuration->updateConfiguration([
            'login_throttling_enabled' => false,
            'login_throttling_max_attempts' => 9,
            'login_throttling_interval' => 30,
            'login_throttling_storage' => ' cache.system ',
        ]));

        $this->assertFalse((bool) $inMemoryConfiguration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED'));
        $this->assertSame(9, $inMemoryConfiguration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS'));
        $this->assertSame(30, $inMemoryConfiguration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL'));
        $this->assertSame('cache.system', $inMemoryConfiguration->get('PS_SECURITY_ADMIN_LOGIN_THROTTLING_STORAGE'));
    }

    public function testUpdateConfigurationReturnsErrorForInvalidLimits(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([]));

        $errors = $configuration->updateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 0,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => '',
        ]);

        $this->assertNotEmpty($errors);
        $this->assertSame(AdminLoginFormThrottlingConfiguration::ERROR_KEY_INVALID_CONFIGURATION, $errors[0]['key']);
    }

    public function testUpdateConfigurationReturnsErrorForInvalidStorage(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([]));

        $errors = $configuration->updateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 3,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => 'cache.invalid',
        ]);

        $this->assertNotEmpty($errors);
        $this->assertSame(AdminLoginFormThrottlingConfiguration::ERROR_KEY_INVALID_STORAGE, $errors[0]['key']);
    }

    public function testValidateConfigurationRejectsInvalidNumbers(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([]));

        $this->assertFalse($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 0,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => '',
        ]));

        $this->assertFalse($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 1,
            'login_throttling_interval' => 0,
            'login_throttling_storage' => '',
        ]));
    }

    public function testFirewallConfigurationUsesStoredValues(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED' => 1,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS' => 12,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL' => 1,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_STORAGE' => 'cache.custom.rate_limiter',
        ]), $this->createContainerWithStorageService('cache.custom.rate_limiter'));

        $this->assertSame(
            [
                'max_attempts' => 12,
                'interval' => '1 minute',
                'storage_service' => 'cache.custom.rate_limiter',
            ],
            $configuration->getFirewallConfiguration()
        );
    }

    public function testDisabledFirewallConfigurationReturnsFalse(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED' => 0,
        ]));

        $this->assertFalse($configuration->getFirewallConfiguration());
    }

    public function testInvalidStoredValuesFallbackToDefaults(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_ENABLED' => 1,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_MAX_ATTEMPTS' => -1,
            'PS_SECURITY_ADMIN_LOGIN_THROTTLING_INTERVAL' => 0,
        ]));

        $this->assertSame(
            [
                'max_attempts' => 5,
                'interval' => '15 minutes',
            ],
            $configuration->getFirewallConfiguration()
        );
    }

    public function testValidateConfigurationRejectsUnknownStorageService(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([]), new DummyContainer([]));

        $this->assertFalse($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 3,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => 'cache.unknown',
        ]));
    }

    public function testValidateConfigurationRejectsServiceWithInvalidType(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(new InMemoryConfiguration([]), new DummyContainer([
            'cache.invalid' => new stdClass(),
        ]));

        $this->assertFalse($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 3,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => 'cache.invalid',
        ]));
    }

    public function testValidateConfigurationAcceptsPsr6CacheService(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(
            new InMemoryConfiguration([]),
            $this->createContainerWithCacheService('cache.app')
        );

        $this->assertTrue($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 3,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => 'cache.app',
        ]));
    }

    public function testValidateConfigurationAcceptsRateLimiterStorageService(): void
    {
        $configuration = new AdminLoginFormThrottlingConfiguration(
            new InMemoryConfiguration([]),
            $this->createContainerWithStorageService('cache.rate_limiter')
        );

        $this->assertTrue($configuration->validateConfiguration([
            'login_throttling_enabled' => true,
            'login_throttling_max_attempts' => 3,
            'login_throttling_interval' => 10,
            'login_throttling_storage' => 'cache.rate_limiter',
        ]));
    }

    private function createContainerWithCacheService(string $serviceId): ContainerInterface
    {
        return new DummyContainer([
            $serviceId => new DummyCacheItemPool(),
        ]);
    }

    private function createContainerWithStorageService(string $serviceId): ContainerInterface
    {
        return new DummyContainer([
            $serviceId => new DummyRateLimiterStorage(),
        ]);
    }
}

class InMemoryConfiguration implements ConfigurationInterface
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(private array $values)
    {
    }

    public function get($key)
    {
        return $this->values[$key] ?? false;
    }

    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }
}

class DummyContainer implements ContainerInterface
{
    /**
     * @param array<string, object> $services
     */
    public function __construct(private array $services)
    {
    }

    public function set(string $id, ?object $service)
    {
        if ($service === null) {
            unset($this->services[$id]);
        } else {
            $this->services[$id] = $service;
        }
    }

    public function get(string $id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        if (!isset($this->services[$id])) {
            if (self::NULL_ON_INVALID_REFERENCE === $invalidBehavior || self::IGNORE_ON_INVALID_REFERENCE === $invalidBehavior || self::IGNORE_ON_UNINITIALIZED_REFERENCE === $invalidBehavior) {
                return null;
            }

            throw new RuntimeException(sprintf('Service "%s" not found.', $id));
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function initialized(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function getParameter(string $name)
    {
        throw new RuntimeException('Not implemented.');
    }

    public function hasParameter(string $name): bool
    {
        return false;
    }

    public function setParameter(string $name, array|bool|string|int|float|UnitEnum|null $value)
    {
        throw new RuntimeException('Not implemented.');
    }
}

class DummyRateLimiterStorage implements StorageInterface
{
    public function save(LimiterStateInterface $limiterState): void
    {
    }

    public function fetch(string $limiterStateId): ?LimiterStateInterface
    {
        return null;
    }

    public function delete(string $limiterStateId): void
    {
    }
}

class DummyCacheItemPool implements CacheItemPoolInterface
{
    public function getItem(string $key): CacheItemInterface
    {
        return new DummyCacheItem($key);
    }

    public function getItems(array $keys = []): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->getItem($key);
        }
    }

    public function hasItem(string $key): bool
    {
        return false;
    }

    public function clear(): bool
    {
        return true;
    }

    public function deleteItem(string $key): bool
    {
        return true;
    }

    public function deleteItems(array $keys): bool
    {
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }
}

class DummyCacheItem implements CacheItemInterface
{
    public function __construct(private string $key)
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return null;
    }

    public function isHit(): bool
    {
        return false;
    }

    public function set(mixed $value): static
    {
        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        return $this;
    }
}
