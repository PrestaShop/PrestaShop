<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

/**
 * Defines constraints for Order message attributes
 */
class OrderMessageConstraint
{
    public const MAX_NAME_LENGTH = 128;

    /**
     * `order_message_lang`.`message` is a mediumtext column and OrderMessage's model already
     * declares the matching size for that field, so the storage was never the limit. The same
     * value bounds the message written on an order, which is prefilled from a predefined one and
     * would otherwise refuse what this form accepts.
     */
    public const MAX_MESSAGE_LENGTH = FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4;
}
