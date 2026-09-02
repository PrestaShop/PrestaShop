<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;

interface GetAttachmentForEditingHandlerInterface
{
    /**
     * @param GetAttachmentForEditing $query
     *
     * @return EditableAttachment
     */
    public function handle(GetAttachmentForEditing $query): EditableAttachment;
}
