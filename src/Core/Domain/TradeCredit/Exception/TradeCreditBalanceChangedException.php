<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception;

/**
 * Thrown when the balance changed between loading the edit form and saving it.
 */
class TradeCreditBalanceChangedException extends TradeCreditException
{
}
