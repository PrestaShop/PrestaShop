<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Exception;

/** Thrown when a csp_log entry cannot be found, or is out of the current shop scope, when trying to allow it. */
final class CspLogNotFoundException extends CspException
{
}
