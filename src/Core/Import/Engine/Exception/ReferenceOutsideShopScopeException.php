<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * A match_ref lookup found the reference in the catalog, but on none of the
 * run's shops: creating a product would silently duplicate the reference, so
 * the row must fail instead.
 */
class ReferenceOutsideShopScopeException extends ImportEngineException
{
}
