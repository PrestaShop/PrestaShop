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
use Db;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Validate\CombinationValidator;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotBulkDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShop\PrestaShop\Core\Repository\ShopConstraintTrait;
use PrestaShopException;

class CombinationRepository extends AbstractMultiShopObjectModelRepository
{
    use ShopConstraintTrait;

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
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param CombinationValidator $combinationValidator
     * @param AttributeRepository $attributeRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CombinationValidator $combinationValidator,
        AttributeRepository $attributeRepository,
        ProductRepository $productRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->combinationValidator = $combinationValidator;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     *
     * @return Combination
     *
     * @throws CoreException
     */
    public function get(CombinationId $combinationId, ShopId $shopId): Combination
    {
        /** @var Combination $combination */
        $combination = $this->getObjectModelForShop(
            $combinationId->getValue(),
            Combination::class,
            CombinationNotFoundException::class,
            $shopId,
            CombinationShopAssociationNotFoundException::class
        );

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     *
     * @return Combination
     *
     * @throws CannotAddCombinationException
     */
    public function create(ProductId $productId, array $shopIds): Combination
    {
        $combination = new Combination();
        $combination->id_product = $productId->getValue();
        $combination->default_on = false;
        $combination->id_shop_list = array_map(function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $shopIds);

        $this->addObjectModelToShops($combination, $shopIds, CannotAddCombinationException::class);

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param int[] $attributeIds
     *
     * @return CombinationId
     */
    public function findCombinationIdByAttributes(ProductId $productId, array $attributeIds): ?CombinationId
    {
        sort($attributeIds);
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->addSelect('pa.id_product_attribute')
            ->addSelect('GROUP_CONCAT(pac.id_attribute ORDER BY pac.id_attribute ASC SEPARATOR "-") AS attribute_ids')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->innerJoin(
                'pa',
                $this->dbPrefix . 'product_attribute_combination',
                'pac',
                'pac.id_product_attribute = pa.id_product_attribute'
            )
            ->andWhere('pa.id_product = :productId')
            ->andHaving('attribute_ids = :attributeIds')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('attributeIds', implode('-', $attributeIds))
            ->addGroupBy('pa.id_product_attribute')
        ;
        $result = $qb->executeQuery()->fetchAssociative();

        if (empty($result)) {
            return null;
        }

        return new CombinationId((int) $result['id_product_attribute']);
    }

    /**
     * @param CombinationId $combinationId
     * @param int[] $attributeIds
     */
    public function saveProductAttributeAssociation(CombinationId $combinationId, array $attributeIds): void
    {
        $this->assertCombinationExists($combinationId);
        $this->attributeRepository->assertAllAttributesExist($attributeIds);

        $attributesList = [];
        foreach ($attributeIds as $attributeId) {
            $attributesList[] = [
                'id_product_attribute' => $combinationId->getValue(),
                'id_attribute' => $attributeId,
            ];
        }

        try {
            if (!Db::getInstance()->insert('product_attribute_combination', $attributesList)) {
                throw new CannotAddCombinationException('Failed saving product-combination associations');
            }
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when saving product-combination associations', 0, $e);
        }
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
        $result = $qb->executeQuery()->fetchAssociative();
        if (empty($result) || empty($result['id_product'])) {
            throw new CombinationNotFoundException(sprintf('Combination #%d was not found', $combinationId->getValue()));
        }

        return new ProductId((int) $result['id_product']);
    }

    /**
     * Creates a new combination in product_attribute_shop assuming it already exists in product_attribute table
     *
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     */
    public function addToShop(CombinationId $combinationId, ShopId $shopId): void
    {
        $productId = $this->getProductId($combinationId);

        $combination = new Combination();
        $combination->id = $combinationId->getValue();
        $combination->force_id = true;
        $combination->id_product = $productId->getValue();
        $combination->default_on = false;

        $this->updateObjectModelForShops($combination, [$shopId], CannotUpdateCombinationException::class);
    }

    /**
     * Copy combination data from one shop to another.
     *
     * @param CombinationId $combinationId
     * @param ShopId $sourceId
     * @param ShopId $targetId
     */
    public function copyToShop(CombinationId $combinationId, ShopId $sourceId, ShopId $targetId): void
    {
        $combination = $this->get($combinationId, $sourceId);
        $this->updateObjectModelForShops($combination, [$targetId], CannotUpdateCombinationException::class);
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     *
     * @return Combination
     *
     * @throws InvalidShopConstraintException
     */
    public function getByShopConstraint(CombinationId $combinationId, ShopConstraint $shopConstraint): Combination
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Combination has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->forAllShops()) {
            try {
                return $this->get($combinationId, $this->getDefaultShopIdForCombination($combinationId));
                // We try to fetch combination for default shop first,
                // but in case it is not associated to default shop,
                // then we load first found associated combination
            } catch (CombinationShopAssociationNotFoundException $e) {
                $associatedShopIds = $this->getAssociatedShopIds($combinationId);
                if (empty($associatedShopIds)) {
                    throw $e;
                }

                return $this->get($combinationId, reset($associatedShopIds));
            }
        } else {
            return $this->get($combinationId, $shopConstraint->getShopId());
        }
    }

    /**
     * @param Combination $combination
     * @param array $updatableProperties
     * @param ShopConstraint $shopConstraint
     * @param int $errorCode
     */
    public function partialUpdate(Combination $combination, array $updatableProperties, ShopConstraint $shopConstraint, int $errorCode): void
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product combination has no features related with shop group use single shop and all shops constraints');
        }

        $this->combinationValidator->validate($combination);
        $combinationId = new CombinationId((int) $combination->id);

        $this->partiallyUpdateObjectModelForShops(
            $combination,
            $updatableProperties,
            $this->getShopIdsByConstraint($combinationId, $shopConstraint),
            CannotAddCombinationException::class,
            $errorCode
        );
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return ShopId
     *
     * @throws ProductNotFoundException
     */
    public function getDefaultShopIdForCombination(CombinationId $combinationId): ShopId
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('p.id_shop_default')
            ->from($this->dbPrefix . 'product', 'p')
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_attribute',
                'pa',
                'pa.id_product = p.id_product'
            )
            ->where('pa.id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (empty($result['id_shop_default'])) {
            throw new ProductNotFoundException(sprintf(
                'Could not find Product by combination id %d',
                $combinationId->getValue()
            ));
        }

        return new ShopId((int) $result['id_shop_default']);
    }

    /**
     * @param CombinationId $combinationId
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function delete(CombinationId $combinationId, ShopConstraint $shopConstraint, int $errorCode = 0): void
    {
        $removedShops = $this->getShopIdsByConstraint($combinationId, $shopConstraint);
        if (empty($removedShops)) {
            return;
        }

        $this->deleteObjectModelFromShops(
            // We get the combination any of the removed ones, it doesn't change much so the first is fine
            $this->get($combinationId, reset($removedShops)),
            $removedShops,
            CannotDeleteCombinationException::class,
            $errorCode
        );
    }

    /**
     * @param CombinationId[] $combinationIds
     * @param ShopConstraint $shopConstraint
     *
     * @throws CannotBulkDeleteCombinationException
     */
    public function bulkDelete(array $combinationIds, ShopConstraint $shopConstraint): void
    {
        $bulkDeleteException = new CannotBulkDeleteCombinationException();

        foreach ($combinationIds as $combinationId) {
            try {
                $this->delete($combinationId, $shopConstraint);
            } catch (CannotDeleteCombinationException $e) {
                $bulkDeleteException->addException($combinationId, $e);
            }
        }

        if ($bulkDeleteException->isEmpty()) {
            return;
        }

        throw $bulkDeleteException;
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteByProductId(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $combinationIds = $this->getCombinationIds($productId, $shopConstraint);

        $this->bulkDelete($combinationIds, $shopConstraint);
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return CombinationId[]
     */
    public function getCombinationIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $shopIds = $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint);
        $shopIds = array_map(function (ShopId $shopId) {
            return $shopId->getValue();
        }, $shopIds);

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('pas.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->andWhere('pas.id_product = :productId')
            ->andWhere($qb->expr()->in('pas.id_shop', ':shopIds'))
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('pas.id_product_attribute', 'ASC')
            ->addGroupBy('pas.id_product_attribute')
        ;

        $combinationIds = $qb->executeQuery()->fetchAllAssociative();

        return array_map(
            function (array $combination) { return new CombinationId((int) $combination['id_product_attribute']); },
            $combinationIds
        );
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return CombinationId|null
     */
    public function findFirstCombinationId(ProductId $productId, ShopConstraint $shopConstraint): ?CombinationId
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Combination has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId();
        } else {
            $shopId = $this->productRepository->getProductDefaultShopId($productId);
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('pas.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->where('pas.id_shop = :shopId')
            ->andWhere('pas.id_product = :productId')
            ->orderBy('id_product_attribute', 'ASC')
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return new CombinationId((int) $result['id_product_attribute']);
    }

    /**
     * Check if combination is associated with certain shop
     *
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     *
     * @return bool
     */
    public function isAssociatedWithShop(CombinationId $combinationId, ShopId $shopId): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('pas.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->where('pas.id_product_attribute = :combinationId')
            ->andWhere('pas.id_shop = :shopId')
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        return isset($result['id_product_attribute']);
    }

    /**
     * Returns default combination ID identified as such in DB by default_on property
     *
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return CombinationId|null
     */
    public function findDefaultCombinationIdForShop(ProductId $productId, ShopId $shopId): ?CombinationId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('pas.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->where('pas.id_product = :productId')
            ->andWhere('pas.id_shop = :shopId')
            ->andWhere('pas.default_on = 1')
            ->addOrderBy('pas.id_product_attribute', 'ASC')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('shopId', $shopId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();
        if (empty($result['id_product_attribute'])) {
            return null;
        }

        return new CombinationId((int) $result['id_product_attribute']);
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return ShopId[]
     *
     * @throws Exception
     * @throws ShopException
     */
    public function getAssociatedShopIds(CombinationId $combinationId): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id_shop')
            ->from($this->dbPrefix . 'product_attribute_shop')
            ->where('id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
            ->addGroupBy('id_shop')
        ;

        return array_map(
            static function (array $result): ShopId {
                return new ShopId((int) $result['id_shop']);
            },
            $qb->executeQuery()->fetchAllAssociative()
        );
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(CombinationId $combinationId, ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('pas.id_shop')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->innerJoin(
                'pas',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = pas.id_shop AND s.id_shop_group = :shopGroupId'
            )
            ->andWhere('pas.id_product_attribute = :combinationId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->setParameter('combinationId', $combinationId->getValue())
            ->addGroupBy('id_shop')
        ;

        return array_map(static function (array $shop) {
            return new ShopId((int) $shop['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
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
     * @param ProductId $productId
     * @param CombinationId $newDefaultCombinationId
     * @param ShopConstraint $shopConstraint
     *
     * @throws ProductNotFoundException
     */
    public function setDefaultCombination(
        ProductId $productId,
        CombinationId $newDefaultCombinationId,
        ShopConstraint $shopConstraint
    ): void {
        $defaultShopId = $this->getDefaultShopIdForCombination($newDefaultCombinationId);
        $shopIds = $this->getShopIdsByConstraint($newDefaultCombinationId, $shopConstraint);

        foreach ($shopIds as $shopId) {
            // we need to update the common table only for default shop, but only when default shop is impacted by the constraint
            if ($defaultShopId->getValue() === $shopId->getValue()) {
                $this->setDefaultCombinationInCommonTable($productId, $newDefaultCombinationId);

                break;
            }
        }

        $this->setDefaultCombinationInShopTable($productId, $newDefaultCombinationId, $shopIds);
    }

    public function updateCombinationOutOfStockType(
        ProductId $productId,
        OutOfStockType $outOfStockType,
        ShopConstraint $shopConstraint
    ): void {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update(sprintf('%sstock_available', $this->dbPrefix), 'ps')
            ->set('ps.out_of_stock', (string) $outOfStockType->getValue())
            ->where('ps.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $this->applyShopConstraint($qb, $shopConstraint)->executeStatement();
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     * @param ShopConstraint $shopConstraint
     * @param string $searchPhrase
     *
     * @return array<int, CombinationAttributeInformation[]>
     *
     * @throws CombinationException
     */
    public function searchProductCombinations(
        ProductId $productId,
        LanguageId $languageId,
        ShopConstraint $shopConstraint,
        string $searchPhrase,
        ?int $limit = null
    ): array {
        $combinationIds = $this->searchCombinationIdsByAttributes(
            $productId,
            $languageId,
            $shopConstraint,
            $searchPhrase,
            $limit
        );

        return $this->attributeRepository->getAttributesInfoByCombinationIds($combinationIds, $languageId);
    }

    /**
     * Sets default_on property to a provided combination in product_attribute table
     *
     * @param ProductId $productId
     * @param CombinationId $newDefaultCombinationId
     */
    private function setDefaultCombinationInCommonTable(ProductId $productId, CombinationId $newDefaultCombinationId): void
    {
        $commonCombinationTable = sprintf('%sproduct_attribute', $this->dbPrefix);

        // find current default combination and make it non-default
        // important to check NULL, because it is impossible to have "0" as falsy value due to sql constraint
        $this->connection->executeStatement(sprintf(
            'UPDATE %s SET default_on = NULL WHERE default_on = 1 AND id_product = %d',
            $commonCombinationTable,
            $productId->getValue()
        ));
        // set new default combination
        $this->connection->executeStatement(sprintf(
            'UPDATE %s SET default_on = 1 WHERE id_product_attribute = %d',
            $commonCombinationTable,
            $newDefaultCombinationId->getValue()
        ));
    }

    /**
     * Sets default_on property to a provided combination in product_attribute_shop table
     *
     * @param ProductId $productId
     * @param CombinationId $newDefaultCombinationId
     * @param ShopId[] $shopIds
     */
    private function setDefaultCombinationInShopTable(
        ProductId $productId,
        CombinationId $newDefaultCombinationId,
        array $shopIds
    ): void {
        if (empty($shopIds)) {
            return;
        }

        $shopCombinationTable = sprintf('%sproduct_attribute_shop', $this->dbPrefix);
        $shopIdsString = implode(
            ',',
            array_map(function (ShopId $shopId): int { return $shopId->getValue(); }, $shopIds)
        );
        // find current default combination and make it non-default
        // important to check NULL, because it is impossible to have "0" as falsy value due to sql constraint
        $this->connection->executeStatement(sprintf(
            'UPDATE %s SET default_on = NULL WHERE default_on = 1 AND id_product = %d AND id_shop IN (%s)',
            $shopCombinationTable,
            $productId->getValue(),
            $shopIdsString
        ));

        // set new default combination
        $this->connection->executeStatement(sprintf(
            'UPDATE %s SET default_on = 1 WHERE id_product_attribute = %d AND id_shop IN (%s)',
            $shopCombinationTable,
            $newDefaultCombinationId->getValue(),
            $shopIdsString
        ));
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    private function getShopIdsByConstraint(CombinationId $combinationId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getAssociatedShopIdsFromGroup($combinationId, $shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->getAssociatedShopIds($combinationId);
        }

        return [$shopConstraint->getShopId()];
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     * @param ShopConstraint $shopConstraint
     * @param string $searchPhrase
     *
     * @return CombinationId[]
     */
    private function searchCombinationIdsByAttributes(
        ProductId $productId,
        LanguageId $languageId,
        ShopConstraint $shopConstraint,
        string $searchPhrase,
        ?int $limit
    ): array {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Group shop constraint is not supported');
        }

        $attributeIds = $this->searchAttributes($languageId, $shopConstraint, $searchPhrase);

        if (empty($attributeIds)) {
            return [];
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('pac.id_product_attribute, pac.id_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
        ;

        if ($shopConstraint->forAllShops()) {
            $qb->innerJoin(
                'pac',
                $this->dbPrefix . 'product_attribute',
                'pa',
                'pac.id_product_attribute = pa.id_product_attribute'
            );
        } else {
            $qb->innerJoin(
                'pac',
                $this->dbPrefix . 'product_attribute_shop',
                'pa',
                'pac.id_product_attribute = pa.id_product_attribute AND pa.id_shop = :shopId'
            )->setParameter('shopId', $shopConstraint->getShopId()->getValue());
        }

        $qb
            ->where('pa.id_product = :productId')
            ->andWhere($qb->expr()->in('pac.id_attribute', ':attributes'))
            ->setParameter('attributes', $attributeIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('productId', $productId->getValue())
        ;

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $results = $qb->executeQuery()->fetchAllAssociative();
        if (!$results) {
            return [];
        }

        return array_map(static function (array $result): CombinationId {
            return new CombinationId((int) $result['id_product_attribute']);
        }, $results);
    }

    /**
     * @param LanguageId $languageId
     * @param ShopConstraint $shopConstraint
     * @param string $searchPhrase
     *
     * @return int[]
     */
    private function searchAttributes(LanguageId $languageId, ShopConstraint $shopConstraint, string $searchPhrase): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop group constraint is not supported');
        }

        $qb = $this->connection->createQueryBuilder();

        $qb->select('a.id_attribute')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->innerJoin(
                'a',
                $this->dbPrefix . 'attribute_lang',
                'al',
                'a.id_attribute = al.id_attribute AND al.id_lang = :languageId'
            )
            ->innerJoin(
                'a',
                $this->dbPrefix . 'attribute_group_lang',
                'agl',
                'a.id_attribute_group = agl.id_attribute_group and agl.id_lang = :languageId'
            )
            ->where('al.name LIKE :searchPhrase')
            ->orWhere('agl.name LIKE :searchPhrase')
            ->orWhere('agl.public_name LIKE :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->setParameter('languageId', $languageId->getValue())
        ;

        if ($shopConstraint->getShopId()) {
            // this makes sure we are searching only in certain shop, so it doesn't return irrelevant attribute ids
            $qb->innerJoin(
                'a',
                $this->dbPrefix . 'attribute_shop',
                'attrShop',
                'a.id_attribute = attrShop.id_attribute AND attrShop.id_shop = :shopId'
            )
                ->innerJoin(
                    'agl',
                    $this->dbPrefix . 'attribute_group_shop', 'ags',
                    'agl.id_attribute_group = ags.id_attribute_group AND ags.id_shop = :shopId'
                )
                ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        }

        $results = $qb->executeQuery()->fetchAllAssociative();

        return array_map('intval', array_column($results, 'id_attribute'));
    }
}
