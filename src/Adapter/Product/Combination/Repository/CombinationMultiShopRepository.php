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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotBulkDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

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
     * @param ProductId $productId
     * @param bool $isDefault
     * @param ShopId $shopId
     *
     * @return Combination
     *
     * @throws CannotAddCombinationException
     */
    public function create(ProductId $productId, bool $isDefault, ShopId $shopId): Combination
    {
        $combination = new Combination(null, null, $shopId->getValue());
        $combination->id_product = $productId->getValue();
        $combination->default_on = $isDefault;
        $combination->id_shop_list = [];

        $this->addObjectModel($combination, CannotAddCombinationException::class);

        return $combination;
    }

    /**
     * @todo: This is for generating combination when it already exists in other shop. (so it doesnt create additional one with different id)
     *        needs more attention, we should probably reset everything and only leave id,
     *        while now it will probably copy values from context shop combination (or wherever it was loaded from)
     *        Also maybe this should be left internally in "create" method?
     */
    public function addToShop(CombinationId $combinationId, ShopId $shopId): void
    {
        /** @var Combination $combination */
        $combination = $this->getObjectModel(
            $combinationId->getValue(),
            Combination::class,
            CombinationNotFoundException::class
        );

        //@todo: dedicated exception and/or code
        $this->updateObjectModelForShops($combination, [$shopId->getValue()], CombinationException::class);
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
            return $this->getCombinationByDefaultShop($combinationId);
        }

        return $this->getCombinationByShopId($combinationId, $shopConstraint->getShopId());
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
        $failedIds = [];
        foreach ($combinationIds as $combinationId) {
            try {
                $this->delete($combinationId, $shopConstraint);
            } catch (CannotDeleteCombinationException $e) {
                $failedIds[] = $combinationId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteCombinationException($failedIds, sprintf(
            'Failed to delete following combinations: %s',
            implode(', ', $failedIds)
        ));
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return Combination|null
     *
     * @throws InvalidShopConstraintException
     */
    public function findDefaultCombination(ProductId $productId, ShopConstraint $shopConstraint): ?Combination
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Combination has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId();
        } else {
            $shopId = $this->getProductDefaultShopId($productId);
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('pas.id_product_attribute, pas.default_on')
            ->from($this->dbPrefix . 'product_attribute_shop', 'pas')
            ->where('pas.id_shop = :shopId')
            ->andWhere('pas.id_product = :productId')
            ->andWhere('pas.default_on = 1')
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetchAssociative();

        if (!isset($result['id_product_attribute'])) {
            return null;
        }

        $combinationId = (int) $result['id_product_attribute'];

        return $this->getCombinationByShopId(new CombinationId($combinationId), $shopId);
    }

    public function findFirstCombinationId(ProductId $productId, ShopConstraint $shopConstraint): ?CombinationId
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Combination has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId();
        } else {
            $shopId = $this->getProductDefaultShopId($productId);
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
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     *
     * @return Combination
     *
     * @throws CoreException
     */
    public function getCombinationByShopId(CombinationId $combinationId, ShopId $shopId): Combination
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
     * @todo: duplicate from ProductMultiShopRepository. How could we reuse it?
     *
     * @param ProductId $productId
     *
     * @return ShopId
     *
     * @throws ProductNotFoundException
     */
    public function getProductDefaultShopId(ProductId $productId): ShopId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop_default')
            ->from($this->dbPrefix . 'product')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetch();
        if (empty($result['id_shop_default'])) {
            throw new ProductNotFoundException(sprintf(
                'Could not find Product with id %d',
                $productId->getValue()
            ));
        }

        return new ShopId((int) $result['id_shop_default']);
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return Combination
     */
    private function getCombinationByDefaultShop(CombinationId $combinationId): Combination
    {
        $defaultShopId = $this->getDefaultShopIdForCombination($combinationId);

        return $this->getCombinationByShopId($combinationId, $defaultShopId);
    }
}
