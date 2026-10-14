<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * Class CacheAdapterFactory responsible for returning the right Cache adapter for the associated driver
 */
class CacheAdapterFactory
{
    /**
     * @param array<int, array{ip: string, port: int|string}> $memcachedServers servers configured in
     *                                                                          Advanced Parameters > Performance
     */
    public function getCacheAdapter(string $driver, array $memcachedServers = []): AdapterInterface
    {
        if ($driver === 'apcu') {
            return new ApcuAdapter();
        } elseif ($driver === 'memcached') {
            return new MemcachedAdapter(
                MemcachedAdapter::createConnection($this->buildServerDsn($memcachedServers), ['lazy' => true])
            );
        }

        return new ArrayAdapter();
    }

    /**
     * The servers set in Configure > Advanced Parameters > Performance are the ones the legacy cache
     * connects to. Pointing this adapter at localhost regardless meant a shop with a custom host or
     * port had one half of its caching honour the setting and the other half ignore it.
     *
     * @param array<int, array{ip: string, port: int|string}> $servers
     *
     * @return string[]|string
     */
    protected function buildServerDsn(array $servers)
    {
        if (empty($servers)) {
            return 'memcached://localhost';
        }

        return array_map(
            static function (array $server): string {
                return sprintf('memcached://%s:%d', $server['ip'], (int) $server['port']);
            },
            $servers
        );
    }
}
