<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception;

/**
 * Is thrown when deleting attribute group fails
 */
class DeleteAttributeGroupException extends AttributeGroupException
{
    /**
     * When trying to delete single attribute group fails
     */
    public const FAILED_DELETE = 10;

    /**
     * When deleting in bulk action fails
     */
    public const FAILED_BULK_DELETE = 20;
}
