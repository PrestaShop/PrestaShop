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
    public function getCacheAdapter(string $driver): AdapterInterface
    {
        if ($driver === 'apcu') {
            return new ApcuAdapter();
        } elseif ($driver === 'memcached') {
            return new MemcachedAdapter(
                AbstractAdapter::createConnection('memcached://localhost', ['lazy' => true])
            );
        }

        return new ArrayAdapter();
    }
}
