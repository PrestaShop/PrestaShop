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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Validate\StockAvailableValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotAddStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotUpdateStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use StockAvailable;

/**
 * Methods to handle StockAvailable data storage
 */
class StockAvailableRepository extends AbstractObjectModelRepository
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
     *
     * @return StockId
     *
     * @throws StockAvailableNotFoundException
     */
    public function getStockIdByProduct(ProductId $productId): StockId
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue());
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
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getForProduct(ProductId $productId): StockAvailable
    {
        $stockId = $this->getStockIdByProduct($productId);

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
    public function getForCombination(CombinationId $combinationId): StockAvailable
    {
        //@todo: multishop not handled
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id_stock_available')
            ->from($this->dbPrefix . 'stock_available')
            ->where('id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
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
    public function create(ProductId $productId): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->id_product = $productId->getValue();
        $shopParams = [];
        try {
            StockAvailable::addSqlShopParams($shopParams);
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
