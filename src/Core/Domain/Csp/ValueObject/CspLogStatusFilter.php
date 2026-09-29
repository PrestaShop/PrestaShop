<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject;

/** The "Status" filter of the CSP log grid, shared by the query builder, the filter form and the tests. */
enum CspLogStatusFilter: string
{
    case VIOLATIONS = 'violations';
    case ALLOWED = 'allowed';
}
