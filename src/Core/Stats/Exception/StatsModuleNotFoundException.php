<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Stats\Exception;

use Exception;

/**
 * Thrown when a "stats*" module referenced by the Stats page cannot be loaded.
 */
class StatsModuleNotFoundException extends Exception
{
}
