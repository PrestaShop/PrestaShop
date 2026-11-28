<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

/**
 * Carries the set of product IDs known to be presented together (a category listing,
 * search results, a "featured products" block, ...) so that the per-product lookups in
 * {@see ProductLazyArray} (new flag, colored variants, images) can be resolved from a
 * single batched query for the whole set instead of one query per product.
 *
 * It is an explicit, method-scoped context handed to the presenter for one presentation
 * pass - not an ambient global - in line with the Context refactoring direction (ADR 0024).
 */
final class ProductPresentationContext
{
    /**
     * @var int[]
     */
    private array $productIds;

    /**
     * Batch results memoized once for the whole set, keyed by an arbitrary concern key.
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * @param int[] $productIds Identifiers of the products that will be presented together
     */
    public function __construct(array $productIds)
    {
        $this->productIds = array_values(array_unique(array_map('intval', $productIds)));
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * Resolve a batched lookup for the whole presented set: the loader is invoked once with
     * every presented product ID on first access, and its result is reused for every product
     * afterwards. This is what turns a per-product query into a single query per listing.
     *
     * @param string $key Concern identifier (e.g. "new", "colored_variants", "images")
     * @param callable(int[]): mixed $loader Receives all presented product IDs, returns the batch result
     *
     * @return mixed The memoized batch result
     */
    public function remember(string $key, callable $loader)
    {
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $loader($this->productIds);
        }

        return $this->cache[$key];
    }
}
