<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage;

/**
 * Defines constraints for Order message attributes
 */
class OrderMessageConstraint
{
    public const MAX_NAME_LENGTH = 128;
    public const MAX_MESSAGE_LENGTH = 1200;
}
