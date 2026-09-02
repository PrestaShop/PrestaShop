<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

/**
 * The resolve-or-create template every resolver follows: look the name up,
 * create the entity when nothing matches, and report what happened exactly
 * ONCE per run.
 *
 * The "quiet" part is the subtle bit worth having in one place: what goes into
 * the cache is a STRIPPED ResolvedEntity carrying the id alone, so the first
 * caller learns that the entity was created (or that the name was ambiguous)
 * and every later row gets a plain id. That is what makes callers emit one
 * warning per run instead of one per row, without them having to track it.
 *
 * Unlike PositiveLookupCacheTrait, misses are cached too — and safely so: a
 * miss here always ends in a creation, so the cached entry is the id of the
 * entity this run just created, not the absence of one.
 *
 * The cache lives for the service lifetime, i.e. ONE batch request.
 */
trait QuietResolutionTrait
{
    /**
     * @var array<string, ResolvedEntity> stripped resolutions, keyed by search term
     */
    protected array $quietResolutions = [];

    /**
     * @param callable(): list<int> $lookup every matching id, lowest first
     * @param callable(): int $create executed only when the lookup matched nothing
     */
    protected function resolveThroughCache(string $cacheKey, callable $lookup, callable $create): ResolvedEntity
    {
        if (isset($this->quietResolutions[$cacheKey])) {
            return $this->quietResolutions[$cacheKey];
        }

        $ids = $lookup();
        $resolved = [] === $ids
            ? new ResolvedEntity($create(), true)
            : new ResolvedEntity($ids[0], false, count($ids));

        $this->quietResolutions[$cacheKey] = new ResolvedEntity($resolved->id);

        return $resolved;
    }
}
