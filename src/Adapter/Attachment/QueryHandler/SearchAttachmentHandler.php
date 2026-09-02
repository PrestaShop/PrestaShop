<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler\SearchAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;

/**
 * Handles @see SearchAttachment query using legacy object model
 */
#[AsQueryHandler]
class SearchAttachmentHandler implements SearchAttachmentHandlerInterface
{
    /**
     * @var AttachmentRepository
     */
    private $repository;

    /**
     * @param AttachmentRepository $repository
     */
    public function __construct(AttachmentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param SearchAttachment $query
     *
     * @return array
     *
     * @throws EmptySearchException
     */
    public function handle(SearchAttachment $query): array
    {
        $attachments = $this->repository->search($query->getSearchPhrase());
        if (empty($attachments)) {
            throw new EmptySearchException(sprintf('No attachments found with search "%s"', $query->getSearchPhrase()));
        }

        $attachmentInfos = [];
        foreach ($attachments as $attachment) {
            $attachmentInfos[] = new AttachmentInformation(
                (int) $attachment['id_attachment'],
                $attachment['name'],
                $attachment['description'],
                $attachment['file_name'],
                $attachment['mime'],
                (int) $attachment['file_size']
            );
        }

        return $attachmentInfos;
    }
}
