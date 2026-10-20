<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
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
     * @param int $defaultLifetime Lifetime, in seconds, given to an entry that does not carry its own.
     *                             0 means the entry never expires, which for a driver outliving the
     *                             request leaves a stale value in place until the cache is cleared by
     *                             hand.
     */
    public function getCacheAdapter(string $driver, int $defaultLifetime = 0): AdapterInterface
    {
        if ($driver === 'apcu') {
            return new ApcuAdapter('', $defaultLifetime);
        } elseif ($driver === 'memcached') {
            return new MemcachedAdapter(
                AbstractAdapter::createConnection('memcached://localhost', ['lazy' => true]),
                '',
                $defaultLifetime
            );
        }

        // The array adapter only lives for the request, so an expiration would add nothing.
        return new ArrayAdapter();
    }
}
