<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception;

use Exception;

/**
 * Thrown when a KPI value cannot be computed from the request, e.g. an unknown cart.
 */
class KpiValueUnavailableException extends Exception
{
}
