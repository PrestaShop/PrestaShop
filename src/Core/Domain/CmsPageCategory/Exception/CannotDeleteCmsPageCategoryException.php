<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception;

/**
 * Is thrown on failure when deleting a cms page category
 */
class CannotDeleteCmsPageCategoryException extends CmsPageCategoryException
{
    /**
     * When fails to delete single cms page category
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete cms page categories on bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
