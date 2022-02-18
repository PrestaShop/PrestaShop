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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Validate\StockAvailableValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotAddStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotUpdateStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShopException;
use StockAvailable;

/**
 * @todo: This class has been added while we progressively migrate each domain to multishop It is used for product
 *        commands for now it wille need to be integrated in combination commands later At that point both repositories
 *        can be merged into one (with StockAvailableRepository) which is used everywhere and we can clean the double repositories.
 */
class StockAvailableMultiShopRepository extends AbstractMultiShopObjectModelRepository
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
     * @var StockAvailableValidator
     */
    private $stockAvailableValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param StockAvailableValidator $stockAvailableValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        StockAvailableValidator $stockAvailableValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->stockAvailableValidator = $stockAvailableValidator;
    }

    /**
     * @param StockAvailable $stockAvailable
     *
     * @throws CoreException
     */
    public function update(StockAvailable $stockAvailable): void
    {
        $this->stockAvailableValidator->validate($stockAvailable);
        $this->updateObjectModel($stockAvailable, CannotUpdateStockAvailableException::class);
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return StockId
     *
     * @throws StockAvailableNotFoundException
     */
    public function getStockIdByProduct(ProductId $productId, ShopId $shopId): StockId
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue(), null, $shopId->getValue());
        if ($stockAvailableId <= 0) {
            throw new StockAvailableNotFoundException(sprintf(
                    'Cannot find StockAvailable for product #%d',
                    $productId->getValue()
                )
            );
        }

        return new StockId($stockAvailableId);
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getForProduct(ProductId $productId, ShopId $shopId): StockAvailable
    {
        $stockId = $this->getStockIdByProduct($productId, $shopId);

        return $this->getStockAvailable($stockId);
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getForCombination(CombinationId $combinationId, ShopId $shopId): StockAvailable
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id_stock_available')
            ->from($this->dbPrefix . 'stock_available')
            ->where('id_product_attribute = :combinationId')
            ->andWhere('id_shop = :shopId')
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
        ;

        $result = $qb->execute()->fetch();

        if (!$result) {
            throw new StockAvailableNotFoundException(sprintf(
                    'Cannot find StockAvailable for combination #%d',
                    $combinationId->getValue()
                )
            );
        }

        return $this->getStockAvailable(new StockId((int) $result['id_stock_available']));
    }

    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function createProductStock(ProductId $productId, ShopId $shopId): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->id_product = $productId->getValue();
        $stockAvailable->id_product_attribute = NoCombinationId::NO_COMBINATION_ID;

        // Use legacy method, it checks if the shop belongs to a ShopGroup that shares stock, in which case the StockAvailable
        // must be assigned to the group not the shop
        $shopParams = [];
        try {
            StockAvailable::addSqlShopParams($shopParams, $shopId->getValue());
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to add StockAvailable shop params #%d', $productId->getValue()),
                0,
                $e
            );
        }

        $stockAvailable->id_shop = $shopParams['id_shop'] ?? 0;
        $stockAvailable->id_shop_group = $shopParams['id_shop_group'] ?? 0;
        $this->addObjectModel($stockAvailable, CannotAddStockAvailableException::class);

        return $stockAvailable;
    }

    /**
     * @param StockId $stockId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIds(StockId $stockId): array
    {
        $subQb = $this->connection->createQueryBuilder();
        $subQb
            ->select('CONCAT(sa2.id_product, \'-\', sa2.id_product_attribute)')
            ->from($this->dbPrefix . 'stock_available', 'sa2')
            ->where('sa2.id_stock_available = :stockId')
        ;

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'stock_available', 'sa')
            ->where($qb->expr()->eq(
                'CONCAT(sa.id_product, \'-\', sa.id_product_attribute)',
                sprintf('(%s)', $subQb->getSQL())
            ))
            ->setParameter('stockId', $stockId->getValue())
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
     * @param StockId $stockId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    private function getStockAvailable(StockId $stockId): StockAvailable
    {
        /** @var StockAvailable $stockAvailable */
        $stockAvailable = $this->getObjectModel(
            $stockId->getValue(),
            StockAvailable::class,
            StockAvailableNotFoundException::class
        );

        return $stockAvailable;
    }
}
