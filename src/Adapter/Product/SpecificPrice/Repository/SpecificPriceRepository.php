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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Validate\SpecificPriceValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\CannotAddSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use SpecificPrice;

/**
 * Methods to access data storage for SpecificPrice
 */
class SpecificPriceRepository extends AbstractObjectModelRepository
{
    /**
     * @var SpecificPriceValidator
     */
    private $specificPriceValidator;

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
     * @param SpecificPriceValidator $specificPriceValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        SpecificPriceValidator $specificPriceValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->specificPriceValidator = $specificPriceValidator;
    }

    /**
     * @param SpecificPrice $specificPrice
     * @param int $errorCode
     *
     * @return SpecificPriceId
     *
     * @throws SpecificPriceConstraintException
     * @throws CoreException
     */
    public function add(SpecificPrice $specificPrice, int $errorCode = 0): SpecificPriceId
    {
        $this->specificPriceValidator->validate($specificPrice);
        $id = $this->addObjectModel($specificPrice, CannotAddSpecificPriceException::class, $errorCode);

        return new SpecificPriceId($id);
    }

    /**
     * @param SpecificPriceId $specificPriceId
     *
     * @return SpecificPrice
     *
     * @throws SpecificPriceNotFoundException
     */
    public function get(SpecificPriceId $specificPriceId): SpecificPrice
    {
        /** @var SpecificPrice $specificPrice */
        $specificPrice = $this->getObjectModel(
            $specificPriceId->getValue(),
            SpecificPrice::class,
            SpecificPriceNotFoundException::class
        );

        return $specificPrice;
    }

    /**
     * @param ProductId $productId
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductSpecificPrices(ProductId $productId, ?int $limit = null, ?int $offset = null, ?array $filters = []): array
    {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $filters)
            ->select('sp.*')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAll();
    }

    /**
     * @param ProductId $productId
     * @param array|null $filters
     *
     * @return int
     */
    public function getProductSpecificPricesCount(ProductId $productId, ?array $filters = []): int
    {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $filters)
            ->select('COUNT(sp.id_specific_price) AS total_specific_prices')
        ;

        return (int) $qb->execute()->fetch()['total_specific_prices'];
    }

    /**
     * @param ProductId $productId
     * @param array|null $filters
     *
     * @return QueryBuilder
     */
    private function getSpecificPricesQueryBuilder(ProductId $productId, ?array $filters): QueryBuilder
    {
        //@todo: filters are not handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'specific_price', 'sp')
            ->where('sp.id_product = :productId')
            ->andWhere('sp.id_cart = 0')
            ->andWhere('sp.id_specific_price_rule = 0')
            ->orderBy('id_specific_price', 'asc')
            ->setParameter('productId', $productId->getValue())
        ;

        return $qb;
    }
}
