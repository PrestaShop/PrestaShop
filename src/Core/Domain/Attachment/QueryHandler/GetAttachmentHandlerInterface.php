<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\Attachment;

/**
 * Defines contract for get attachment handler
 */
interface GetAttachmentHandlerInterface
{
    /**
     * @param GetAttachment $query
     *
     * @return Attachment
     */
    public function handle(GetAttachment $query): Attachment;
}
