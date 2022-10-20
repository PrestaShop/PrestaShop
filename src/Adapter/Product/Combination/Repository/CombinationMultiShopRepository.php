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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotBulkDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShopException;

/**
 * @todo: This class has been added while we progressively migrate each domain to multishop It contains the new
 *        dedicated function bound with multishop When everything has been migrated they will be moved back to
 *        the initial CombinationRepository and single shop methods should be removed But since this will be done
 *        in several PRs for now it's easier to separate them into two services
 *        This is why a lot of code is duplicated between the two classes but don't worry this one is only temporary
 */
class CombinationMultiShopRepository extends AbstractMultiShopObjectModelRepository
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
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param CombinationValidator $combinationValidator
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CombinationValidator $combinationValidator,
        AttributeRepository $attributeRepository,
        ProductMultiShopRepository $productRepository
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
            $shopId
        );

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     * @param bool $isDefault
     *
     * @return Combination
     *
     * @throws CannotAddCombinationException
     */
    public function create(ProductId $productId, ShopId $shopId, bool $isDefault = false): Combination
    {
        $combination = new Combination(null, null, $shopId->getValue());
        $combination->id_product = $productId->getValue();
        $combination->default_on = $isDefault;
        $combination->id_shop_list = [$shopId->getValue()];

        $this->addObjectModelToShop($combination, $shopId->getValue(), CannotAddCombinationException::class);

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
        $result = $qb->execute()->fetchAssociative();

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

        // @todo: need to check if these doesn't exist?
        // especially when dealing with combination which has to be only added to another shop,
        // then we get duplicate insert error :/
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
        $result = $qb->execute()->fetchAssociative();
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

        $this->updateObjectModelForShops($combination, [$shopId->getValue()], CannotUpdateCombinationException::class);
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint $shopConstraint
     *
     * @return Combination
     *
     * @throws CoreException
     */
    public function getByShopConstraint(CombinationId $combinationId, ShopConstraint $shopConstraint): Combination
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Combination has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->forAllShops()) {
            $shopId = $this->getDefaultShopIdForCombination($combinationId);
        } else {
            $shopId = $shopConstraint->getShopId();
        }

        return $this->get($combinationId, $shopId);
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

        $result = $qb->execute()->fetch();

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
        $this->deleteObjectModel(
            $this->getByShopConstraint($combinationId, $shopConstraint),
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

        $result = $qb->execute()->fetchAssociative();

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

        $result = $qb->execute()->fetchAssociative();

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

        $result = $qb->execute()->fetchAssociative();
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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'product_attribute_shop')
            ->where('id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
        ;

        $result = $qb->execute()->fetchAll();
        if (empty($result)) {
            return [];
        }

        $shops = [];
        foreach ($result as $shop) {
            $shops[] = new ShopId((int) $shop['id_shop']);
        }

        return $shops;
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
     * @throws InvalidShopConstraintException
     * @throws ProductNotFoundException
     */
    public function setDefaultCombination(
        ProductId $productId,
        CombinationId $newDefaultCombinationId,
        ShopConstraint $shopConstraint
    ): void {
        $defaultShopId = $this->getDefaultShopIdForCombination($newDefaultCombinationId);
        $constraintShopIds = $this->getShopIdsByConstraint($newDefaultCombinationId, $shopConstraint);

        // we need to update the common table only for default shop, but only when default shop is impacted by the constraint
        if (in_array($defaultShopId->getValue(), $constraintShopIds, true)) {
            $this->setDefaultCombinationInCommonTable($productId, $newDefaultCombinationId);
        }

        $this->setDefaultCombinationInShopTable($productId, $newDefaultCombinationId, $constraintShopIds);
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
     * @param int[] $shopIds
     */
    private function setDefaultCombinationInShopTable(
        ProductId $productId,
        CombinationId $newDefaultCombinationId,
        array $shopIds
    ): void {
        if (empty($shopIds)) {
            return;
        }

        $shopIdsString = implode(',', $shopIds);
        $shopCombinationTable = sprintf('%sproduct_attribute_shop', $this->dbPrefix);

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
     * @return int[]
     *
     * @throws InvalidShopConstraintException
     */
    private function getShopIdsByConstraint(CombinationId $combinationId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product combinations has no features related with shop group use single shop and all shops constraints');
        }

        $shopIds = [];
        if ($shopConstraint->forAllShops()) {
            $shops = $this->getAssociatedShopIds($combinationId);
            foreach ($shops as $shopId) {
                $shopIds[] = $shopId->getValue();
            }
        } else {
            $shopIds = [$shopConstraint->getShopId()->getValue()];
        }

        return $shopIds;
    }
}
