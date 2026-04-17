<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception;

/**
 * Is thrown when attribute deletion fails
 */
class DeleteAttributeException extends AttributeException
{
    /**
     * When fails to delete single attribute
     */
    public const FAILED_DELETE = 10;

    /**
     * When deleting fails in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
