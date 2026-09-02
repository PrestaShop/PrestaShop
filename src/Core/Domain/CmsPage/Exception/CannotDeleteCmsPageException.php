<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception;

/**
 * Is thrown on failure when deleting cms page
 */
class CannotDeleteCmsPageException extends CmsPageException
{
    /**
     * When fails to delete single cms page
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete cms pages on bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
