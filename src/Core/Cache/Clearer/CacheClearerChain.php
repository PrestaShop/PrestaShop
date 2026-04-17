<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cache\Clearer;

/**
 * Class CacheClearerChain clears entire PrestaShop cache.
 */
final class CacheClearerChain implements CacheClearerInterface
{
    /**
     * @var CacheClearerInterface[]
     */
    private $cacheClearers;

    /**
     * @param CacheClearerInterface ...$cacheClearers
     */
    public function __construct(CacheClearerInterface ...$cacheClearers)
    {
        $this->cacheClearers = $cacheClearers;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        foreach ($this->cacheClearers as $cacheClearer) {
            $cacheClearer->clear();
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    }
}
