<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Exception;

class CannotDeleteCartRuleException extends CartRuleException
{
    /**
     * When fails to delete single cart rule
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete cart rule in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
