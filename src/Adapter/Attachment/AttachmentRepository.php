<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attachment;

use Attachment;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentAssociatedProduct;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\ProductAttachmentMatch;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
     * @return AttachmentAssociatedProduct[]
     */
    public function getAssociatedProducts(AttachmentId $attachmentId, LanguageId $languageId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->createProductAttachmentQueryBuilder($languageId, $shopConstraint)
            ->where('pa.`id_attachment` = :attachmentId')
            ->setParameter('attachmentId', $attachmentId->getValue())
            ->orderBy('p.`id_product`', 'ASC')
        ;

        return array_map(
            fn (array $row): AttachmentAssociatedProduct => $this->buildAssociatedProduct($row),
            $qb->executeQuery()->fetchAllAssociative()
        );
    }

    /**
     * @return ProductAttachmentMatch[]
     */
    public function searchByProduct(string $phrase, LanguageId $languageId, ShopConstraint $shopConstraint, int $limit): array
    {
        $qb = $this->createProductAttachmentQueryBuilder($languageId, $shopConstraint)
            ->addSelect('pa.`id_attachment`, al.`name` AS attachment_name')
            ->leftJoin(
                'pa',
                $this->dbPrefix . 'attachment_lang',
                'al',
                'al.`id_attachment` = pa.`id_attachment` AND al.`id_lang` = :languageId'
            )
            ->setParameter('phrase', '%' . addcslashes($phrase, '%_\\') . '%')
            ->orderBy('p.`reference`', 'ASC')
            ->addOrderBy('p.`id_product`', 'ASC')
            ->addOrderBy('pa.`id_attachment`', 'ASC')
            ->setMaxResults($limit)
        ;

        $conditions = [
            $qb->expr()->like('p.`reference`', ':phrase'),
            $qb->expr()->like('pl.`name`', ':phrase'),
        ];
        // The ID must match exactly, a part of it would find nearly every product
        if (ctype_digit($phrase)) {
            $conditions[] = $qb->expr()->eq('p.`id_product`', ':productId');
            $qb->setParameter('productId', (int) $phrase);
        }
        $qb->where($qb->expr()->or(...$conditions));

        return array_map(
            fn (array $row): ProductAttachmentMatch => new ProductAttachmentMatch(
                $this->buildAssociatedProduct($row),
                (int) $row['id_attachment'],
                (string) $row['attachment_name'],
            ),
            $qb->executeQuery()->fetchAllAssociative()
        );
    }

    /**
     * Products linked to attachments, with their name and status in the constraint's shop, or in each
     * product's default shop when the constraint is not a single shop.
     */
    private function createProductAttachmentQueryBuilder(LanguageId $languageId, ShopConstraint $shopConstraint): QueryBuilder
    {
        $shopId = $shopConstraint->getShopId();
        $productShop = null !== $shopId ? ':shopId' : 'p.`id_shop_default`';

        $qb = $this->connection->createQueryBuilder()
            ->select('p.`id_product`, p.`reference`, pl.`name`, ps.`active`')
            ->from($this->dbPrefix . 'product_attachment', 'pa')
            ->innerJoin('pa', $this->dbPrefix . 'product', 'p', 'p.`id_product` = pa.`id_product`')
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.`id_product` = p.`id_product` AND ps.`id_shop` = ' . $productShop
            )
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :languageId AND pl.`id_shop` = ' . $productShop
            )
            ->setParameter('languageId', $languageId->getValue())
        ;

        if (null !== $shopId) {
            $qb->setParameter('shopId', $shopId->getValue());
        }

        return $qb;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function buildAssociatedProduct(array $row): AttachmentAssociatedProduct
    {
        return new AttachmentAssociatedProduct(
            (int) $row['id_product'],
            (string) $row['reference'],
            (string) $row['name'],
            (bool) $row['active'],
        );
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
