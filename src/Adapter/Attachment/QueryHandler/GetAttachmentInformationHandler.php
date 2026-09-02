<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler\GetAttachmentInformationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;

/**
 * Handles @see GetAttachmentInformation query using legacy object model
 */
#[AsQueryHandler]
class GetAttachmentInformationHandler implements GetAttachmentInformationHandlerInterface
{
    /**
     * @var AttachmentRepository
     */
    private $attachmentRepository;

    public function __construct(
        AttachmentRepository $attachmentRepository
    ) {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetAttachmentInformation $query): AttachmentInformation
    {
        $attachment = $this->attachmentRepository->get($query->getAttachmentId());

        return new AttachmentInformation(
            (int) $attachment->id,
            $attachment->name,
            $attachment->description,
            $attachment->file_name,
            $attachment->mime,
            (int) $attachment->file_size
        );
    }
}
