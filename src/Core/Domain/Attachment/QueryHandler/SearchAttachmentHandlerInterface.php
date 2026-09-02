<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;

interface SearchAttachmentHandlerInterface
{
    /**
     * @param SearchAttachment $query
     *
     * @return AttachmentInformation[]
     */
    public function handle(SearchAttachment $query): array;
}
