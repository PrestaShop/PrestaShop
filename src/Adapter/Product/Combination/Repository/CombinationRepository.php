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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Repository;

use Combination;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Validate\CombinationValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotBulkDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductCombinationQueryBuilder;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

/**
 * Provides access to Combination data source
 */
class CombinationRepository extends AbstractObjectModelRepository
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
     * @var CombinationValidator
     */
    private $combinationValidator;

    /**
     * @var ProductCombinationQueryBuilder
     */
    private $combinationQueryBuilder;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param CombinationValidator $combinationValidator
     * @param ProductCombinationQueryBuilder $combinationQueryBuilder
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CombinationValidator $combinationValidator,
        ProductCombinationQueryBuilder $combinationQueryBuilder
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->combinationValidator = $combinationValidator;
        $this->combinationQueryBuilder = $combinationQueryBuilder;
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return Combination
     *
     * @throws CombinationNotFoundException
     */
    public function get(CombinationId $combinationId): Combination
    {
        /** @var Combination $combination */
        $combination = $this->getObjectModel(
            $combinationId->getValue(),
            Combination::class,
            CombinationNotFoundException::class
        );

        return $combination;
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return ProductId
     */
    public function getProductId(CombinationId $combinationId): ProductId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('pa.id_product')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->andWhere('pa.id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
        ;
        $result = $qb->execute()->fetchAssociative();
        if (empty($result) || empty($result['id_product'])) {
            throw new CombinationNotFoundException(sprintf('Combination #%d was not found', $combinationId->getValue()));
        }

        return new ProductId((int) $result['id_product']);
    }

    /**
     * @param Combination $combination
     * @param array $updatableProperties
     * @param int $errorCode
     */
    public function partialUpdate(Combination $combination, array $updatableProperties, int $errorCode): void
    {
        $this->combinationValidator->validate($combination);
        $this->partiallyUpdateObjectModel(
            $combination,
            $updatableProperties,
            CannotUpdateCombinationException::class,
            $errorCode
        );
    }

    /**
     * @param CombinationId $combinationId
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function delete(CombinationId $combinationId, int $errorCode = 0): void
    {
        $this->deleteObjectModel($this->get($combinationId), CannotDeleteCombinationException::class, $errorCode);
    }

    /**
     * @param ProductId $productId
     */
    public function deleteByProductId(ProductId $productId): void
    {
        $combinationIds = $this->getCombinationIds($productId);

        $this->bulkDelete($combinationIds);
    }

    /**
     * @param CombinationId[] $combinationIds
     *
     * @throws CannotBulkDeleteCombinationException
     */
    public function bulkDelete(array $combinationIds): void
    {
        $bulkException = null;

        foreach ($combinationIds as $combinationId) {
            try {
                $this->delete($combinationId);
            } catch (CannotDeleteCombinationException $e) {
                if (null === $bulkException) {
                    $bulkException = new CannotBulkDeleteCombinationException('Errors occurred during bulk deletion of combinations');
                }
                $bulkException->addException($combinationId, $e);
            }
        }

        if (null !== $bulkException) {
            throw $bulkException;
        }
    }

    /**
     * @param ProductId $productId
     * @param ProductCombinationFilters|null $filters
     *
     * @return CombinationId[]
     */
    public function getCombinationIds(ProductId $productId, ?ProductCombinationFilters $filters = null): array
    {
        if ($filters) {
            $qb = $this->combinationQueryBuilder->getSearchQueryBuilder($filters)
                ->select('pa.id_product_attribute')
            ;
        } else {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('pa.id_product_attribute')
                ->from($this->dbPrefix . 'product_attribute', 'pa')
                ->andWhere('pa.id_product = :productId')
                ->setParameter('productId', $productId->getValue())
                ->addOrderBy('pa.id_product_attribute', 'ASC')
            ;
        }

        $combinationIds = $qb->execute()->fetchAllAssociative();

        return array_map(
            function (array $combination) { return new CombinationId((int) $combination['id_product_attribute']); },
            $combinationIds
        );
    }

    /**
     * @param CombinationId $combinationId
     *
     * @throws CoreException
     */
    public function assertCombinationExists(CombinationId $combinationId): void
    {
        $this->assertObjectModelExists(
            $combinationId->getValue(),
            'product_attribute',
            CombinationNotFoundException::class
        );
    }

    /**
     * Returns default combination ID identified as such in DB by default_on property
     *
     * @param ProductId $productId
     *
     * @return CombinationId|null
     */
    public function getDefaultCombinationId(ProductId $productId): ?CombinationId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('pa.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->andWhere('pa.default_on = 1')
            ->addOrderBy('pa.id_product_attribute', 'ASC')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetchAssociative();
        if (empty($result['id_product_attribute'])) {
            return null;
        }

        return new CombinationId((int) $result['id_product_attribute']);
    }
}
