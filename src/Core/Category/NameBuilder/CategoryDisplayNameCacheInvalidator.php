<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Category\NameBuilder;

use Psr\Cache\CacheItemPoolInterface;

final class CategoryDisplayNameCacheInvalidator
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    public function invalidate(): void
    {
        $this->cache->clear();
    }
}
