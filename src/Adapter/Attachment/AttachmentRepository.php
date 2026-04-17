<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment;

use Attachment;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access Attachment data source
 */
class AttachmentRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param AttachmentId $attachmentId
     *
     * @return Attachment
     *
     * @throws CoreException
     * @throws AttachmentNotFoundException
     */
    public function get(AttachmentId $attachmentId): Attachment
    {
        /** @var Attachment $attachment */
        $attachment = $this->getObjectModel(
            $attachmentId->getValue(),
            Attachment::class,
            AttachmentNotFoundException::class
        );

        return $attachment;
    }

    /**
     * @param ProductId $productId
     *
     * @return array<int, array<string, string|array<int, string>>>
     */
    public function getProductAttachments(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.*')
            ->from($this->dbPrefix . 'attachment', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'product_attachment',
                'pa',
                'a.id_attachment = pa.id_attachment'
            )
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $results = $qb->executeQuery()->fetchAllAssociative();

        if (empty($results)) {
            return [];
        }

        return $this->addLocalizedValues($results);
    }

    /**
     * @param AttachmentId $attachmentId
     *
     * @throws CoreException
     */
    public function assertAttachmentExists(AttachmentId $attachmentId): void
    {
        $this->assertObjectModelExists($attachmentId->getValue(), 'attachment', AttachmentNotFoundException::class);
    }

    public function search(string $searchPhrase): array
    {
        $searchPhrase = sprintf('%%%s%%', strtolower($searchPhrase));
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.*')
            ->from($this->dbPrefix . 'attachment', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'product_attachment',
                'pa',
                'a.id_attachment = pa.id_attachment'
            )
            ->leftJoin(
                'a',
                $this->dbPrefix . 'attachment_lang',
                'al',
                'al.id_attachment = a.id_attachment'
            )
            ->andWhere(
                $qb->expr()->or(
                    $qb->expr()->like('LOWER(a.file_name)', ':searchPhrase'),
                    $qb->expr()->like('LOWER(al.name)', ':searchPhrase'),
                    $qb->expr()->like('LOWER(al.description)', ':searchPhrase')
                )
            )
            ->setParameter('searchPhrase', $searchPhrase)
            ->addGroupBy('a.id_attachment')
        ;

        $results = $qb->executeQuery()->fetchAllAssociative();

        if (empty($results)) {
            return [];
        }

        return $this->addLocalizedValues($results);
    }

    /**
     * @param array $results
     *
     * @return array
     */
    private function addLocalizedValues(array $results): array
    {
        $attachmentIds = array_map(function (array $result) {
            return (int) $result['id_attachment'];
        }, $results);

        $localizedValuesByAttachmentIds = $this->getAttachmentsLocalizedValues($attachmentIds);

        $fullAttachments = [];
        foreach ($results as $result) {
            foreach ($localizedValuesByAttachmentIds as $attachmentId => $localizedValues) {
                if ($attachmentId !== (int) $result['id_attachment']) {
                    continue;
                }
                $fullAttachments[] = array_merge($result, $localizedValues);
            }
        }

        return $fullAttachments;
    }

    /**
     * @param int[] $attachmentIds
     *
     * @return array<int, array<string, array<int, string>>>
     */
    private function getAttachmentsLocalizedValues(array $attachmentIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('al.*')
            ->from($this->dbPrefix . 'attachment_lang', 'al')
            ->where($qb->expr()->in('id_attachment', ':attachmentIds'))
            ->setParameter('attachmentIds', $attachmentIds, Connection::PARAM_INT_ARRAY)
        ;

        $results = $qb->executeQuery()->fetchAllAssociative();

        $localizedAttachments = [];
        foreach ($results as $result) {
            $localizedAttachments[(int) $result['id_attachment']]['name'][(int) $result['id_lang']] = $result['name'];
            $localizedAttachments[(int) $result['id_attachment']]['description'][(int) $result['id_lang']] = $result['description'];
        }

        return $localizedAttachments;
    }
}
