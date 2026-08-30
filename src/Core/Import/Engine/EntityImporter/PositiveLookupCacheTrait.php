<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

/**
 * The id-list memo shared by every finder and resolver: one lookup per distinct
 * search term instead of one per row (import files repeat the same supplier,
 * shop or feature name on every line).
 *
 * ONLY POSITIVE results are cached, and that is a correctness rule rather than
 * an optimization detail: the import CREATES entities while it runs (the
 * database phase creates products, the resolvers create categories, brands and
 * features), and one batch budget may span a phase boundary, so a term that
 * matches nothing now can match something a few rows later. A cached miss would
 * turn that into a wrong answer, while a cached hit can never go stale — an
 * existing row is never deleted during a run.
 *
 * The cache lives for the service lifetime, i.e. ONE batch request.
 */
trait PositiveLookupCacheTrait
{
    /**
     * @var array<string, list<int>> matched ids, keyed by search term
     */
    protected array $positiveLookupCache = [];

    /**
     * @param callable(): list<int> $lookup executed only on a cache miss
     *
     * @return list<int>
     */
    protected function remember(string $cacheKey, callable $lookup): array
    {
        if (isset($this->positiveLookupCache[$cacheKey])) {
            return $this->positiveLookupCache[$cacheKey];
        }

        $ids = $lookup();
        if ([] !== $ids) {
            $this->positiveLookupCache[$cacheKey] = $ids;
        }

        return $ids;
    }
}
