<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception;

use Exception;

/**
 * Thrown when no KpiRefreshProviderInterface is registered for the requested KPI key.
 */
class UnknownKpiException extends Exception
{
}
