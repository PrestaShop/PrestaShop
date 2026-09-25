<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ActivityLog;

/**
 * Logs successful Back Office activities without exposing infrastructure details to Core.
 */
interface AdminActivityLoggerInterface
{
    public function log(AdminActivity $activity): void;
}
