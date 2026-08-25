<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Contract of a RESOLVE-OR-CREATE lookup: given one raw cell value, always come
 * back with an id, creating the entity when nothing matched. That possibility of
 * creating is exactly what justifies the resolve*() verb and separates these
 * services from the match-only finders.
 *
 * Like finders, resolvers return pure data and never build messages: whether an
 * auto-creation or an ambiguous name deserves a warning is the caller's call.
 *
 * CategoryResolver and FeatureResolver deliberately stay outside this interface:
 * a category needs its parent and a feature value needs its feature, so their
 * signatures carry more than one value.
 */
interface EntityResolverInterface
{
    public function resolve(string $value, ImportRunContext $context): ResolvedEntity;
}
