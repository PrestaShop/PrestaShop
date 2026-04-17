<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;

/**
 * Provides data for attachment add/edit forms
 */
final class AttachmentFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($attachmentId)
    {
        /** @var EditableAttachment $editableAttachment */
        $editableAttachment = $this->queryBus->handle(new GetAttachmentForEditing((int) $attachmentId));

        $data = [
            'attachment_id' => $attachmentId,
            'name' => $editableAttachment->getName(),
            'file_name' => $editableAttachment->getFileName(),
            'file_description' => $editableAttachment->getDescription(),
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [];
    }
}
