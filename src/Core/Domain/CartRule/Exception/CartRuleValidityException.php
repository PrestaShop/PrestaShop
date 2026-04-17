<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Exception;

use PrestaShop\PrestaShop\Core\Exception\TranslatedException;

/**
 * Thrown when cart rule validation fails.
 * This exception must contain already translated error message, which can be displayed to end-user
 */
class CartRuleValidityException extends TranslatedException
{
}
