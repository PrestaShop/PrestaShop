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
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotAddSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotUpdateSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
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
     * @param SpecificPrice $specificPrice
     * @param string[] $updatableProperties
     */
    public function partialUpdate(SpecificPrice $specificPrice, array $updatableProperties): void
    {
        $this->specificPriceValidator->validate($specificPrice);
        $this->partiallyUpdateObjectModel(
            $specificPrice,
            $updatableProperties,
            CannotUpdateSpecificPriceException::class
        );
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $langId
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, mixed> $filters
     *
     * @return array<int, array<string, string|null>>
     */
    public function getProductSpecificPrices(
        ProductId $productId,
        LanguageId $langId,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = []
    ): array
    {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $langId, $filters)
            ->select('
                sp.id_specific_price,
                sp.reduction_type,
                sp.reduction,
                sp.reduction_tax,
                sp.price,
                sp.from_quantity,
                sp.id_customer,
                sp.from,
                sp.to,
                shop.name as shop_name,
                currency.name as currency_name,
                customer.firstname as customer_firstname,
                customer.lastname as customer_lastname,
                country_lang.name as country_name,
                gl.name as group_name'
            )
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $langId
     * @param array<string, mixed> $filters
     *
     * @return int
     */
    public function getProductSpecificPricesCount(ProductId $productId, LanguageId $langId, array $filters = []): int
    {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $langId, $filters)
            ->select('COUNT(sp.id_specific_price) AS total_specific_prices')
        ;

        return (int) $qb->execute()->fetch()['total_specific_prices'];
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $langId
     * @param array<string, mixed> $filters
     *
     * @return QueryBuilder
     */
    private function getSpecificPricesQueryBuilder(ProductId $productId, LanguageId $langId, array $filters): QueryBuilder
    {
        //@todo: filters are not handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'specific_price', 'sp')
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'currency', 'currency',
                'sp.id_currency = currency.id_currency'
            )
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'customer', 'customer',
                'sp.id_customer = customer.id_customer'
            )
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'shop', 'shop',
                'sp.id_shop = shop.id_shop'
            )
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'country_lang',
                'country_lang',
                'sp.id_country = country_lang.id_country AND country_lang.id_lang = :langId'
            )
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'group_lang',
                'gl',
                'sp.id_group = gl.id_group AND gl.id_lang = :langId'
            )
            ->where('sp.id_product = :productId')
            ->andWhere('')
            ->andWhere('sp.id_cart = 0')
            ->andWhere('sp.id_specific_price_rule = 0')
            ->orderBy('id_specific_price', 'asc')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('langId', $langId->getValue())
        ;

        return $qb;
    }
}
