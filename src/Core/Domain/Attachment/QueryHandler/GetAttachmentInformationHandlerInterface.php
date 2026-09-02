<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;

/**
 * Defines contract to handle @see GetAttachmentInformation query
 */
interface GetAttachmentInformationHandlerInterface
{
    /**
     * @param GetAttachmentInformation $query
     *
     * @return AttachmentInformation
     */
    public function handle(GetAttachmentInformation $query): AttachmentInformation;
}
