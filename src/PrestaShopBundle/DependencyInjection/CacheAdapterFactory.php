<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection;

use Memcached;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * Class CacheAdapterFactory responsible for returning the right Cache adapter for the associated driver
 */
class CacheAdapterFactory
{
    public function getCacheAdapter(string $driver, ?MemcacheServerManager $serverManager = null): AdapterInterface
    {
        if ($driver === 'apcu') {
            return new ApcuAdapter();
        } elseif ($driver === 'memcached') {
            return new MemcachedAdapter($this->createMemcachedConnection($serverManager));
        }

        return new ArrayAdapter();
    }

    public function createMemcachedConnection(?MemcacheServerManager $serverManager): Memcached
    {
        return MemcachedAdapter::createConnection($this->getMemcachedServers($serverManager), ['lazy' => true]);
    }

    /**
     * @return array<int, array{0: string, 1: int, 2: int}>|string
     */
    private function getMemcachedServers(?MemcacheServerManager $serverManager): array|string
    {
        $servers = $serverManager?->getServers() ?? [];
        if (empty($servers)) {
            return 'memcached://localhost';
        }

        return array_map(
            static fn (array $server): array => [$server['ip'], (int) $server['port'], (int) $server['weight']],
            $servers
        );
    }
}
