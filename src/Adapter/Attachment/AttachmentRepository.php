<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
