<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Refuses a removal that would leave a merchandise return with zero product lines.
 *
 * Mirrors the legacy guard in AdminReturnController::postProcess (`countProduct() > 1`).
 */
class CannotDeleteLastProductFromOrderReturnException extends OrderReturnException
{
}
