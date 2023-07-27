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
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Validate\SpecificPriceValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotAddSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotDeleteSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotUpdateSpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;
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
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param SpecificPriceValidator $specificPriceValidator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        SpecificPriceValidator $specificPriceValidator,
        ConfigurationInterface $configuration
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->specificPriceValidator = $specificPriceValidator;
        $this->configuration = $configuration;
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
        $this->assertSpecificPriceIsUniquePerProduct($specificPrice);
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
     * @param SpecificPriceId $specificPriceId
     *
     * @return void
     */
    public function delete(SpecificPriceId $specificPriceId): void
    {
        $objectModel = $this->getObjectModel(
            $specificPriceId->getValue(),
            SpecificPrice::class,
            SpecificPriceNotFoundException::class
        );

        $this->deleteObjectModel($objectModel, CannotDeleteSpecificPriceException::class);
    }

    /**
     * @param SpecificPrice $specificPrice
     * @param string[] $updatableProperties
     */
    public function partialUpdate(SpecificPrice $specificPrice, array $updatableProperties): void
    {
        $this->specificPriceValidator->validate($specificPrice);
        $this->assertSpecificPriceIsUniquePerProduct($specificPrice);
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
    ): array {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $langId, $filters)
            ->select('
                sp.*,
                shop.name as shop_name,
                currency_lang.name as currency_name,
                currency.iso_code as currency_iso_code,
                customer.firstname as customer_firstname,
                customer.lastname as customer_lastname,
                country_lang.name as country_name,
                gl.name as group_name'
            )
            ->setFirstResult($offset ?? 0)
            ->setMaxResults($limit)
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param ProductId $productId
     *
     * @return SpecificPriceId[]
     */
    public function getProductSpecificPricesIds(ProductId $productId): array
    {
        return array_map(static function (array $specificPrice): SpecificPriceId {
            return new SpecificPriceId((int) $specificPrice['id_specific_price']);
        }, $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'specific_price', 'sp')
            ->select('sp.id_specific_price')
            ->where('sp.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->executeQuery()->fetchAllAssociative());
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $langId
     * @param array<string, mixed> $filters
     *
     * @return int
     */
    public function countProductSpecificPrices(ProductId $productId, LanguageId $langId, array $filters = []): int
    {
        $qb = $this->getSpecificPricesQueryBuilder($productId, $langId, $filters)
            ->select('COUNT(sp.id_specific_price) AS total_specific_prices')
        ;

        return (int) $qb->executeQuery()->fetchAssociative()['total_specific_prices'];
    }

    /**
     * Finds id of specific price by properties which defines its uniqueness
     *
     * @param int $productId
     * @param int $combinationId
     * @param int $shopId
     * @param int $groupId
     * @param int $countryId
     * @param int $currencyId
     * @param int $customerId
     * @param int $fromQuantity
     * @param string $durationFrom
     * @param string $durationTo
     *
     * @return SpecificPriceId|null
     *
     * @throws CoreException
     * @throws SpecificPriceConstraintException
     */
    public function findExisting(
        int $productId,
        int $combinationId,
        int $shopId,
        int $groupId,
        int $countryId,
        int $currencyId,
        int $customerId,
        int $fromQuantity,
        string $durationFrom,
        string $durationTo
    ): ?SpecificPriceId {
        try {
            $id = (int) SpecificPrice::exists(
                $productId,
                $combinationId,
                $shopId,
                $groupId,
                $countryId,
                $currencyId,
                $customerId,
                $fromQuantity,
                $durationFrom,
                $durationTo
            );
        } catch (PrestaShopException $e) {
            throw new CoreException(
                'Something went wrong when trying to find existing specific price',
                0,
                $e->getPrevious()
            );
        }

        if (!$id) {
            return null;
        }

        return new SpecificPriceId($id);
    }

    /**
     * @param ProductId $productId
     *
     * @return PriorityList|null
     */
    public function findPrioritiesForProduct(ProductId $productId): ?PriorityList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('spp.priority')
            ->from($this->dbPrefix . 'specific_price_priority', 'spp')
            ->where('spp.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->executeQuery()->fetchOne();

        if (!$result) {
            return null;
        }

        return new PriorityList(explode(';', $result));
    }

    /**
     * @return PriorityList
     *
     * @throws CoreException
     */
    public function getDefaultPriorities(): PriorityList
    {
        try {
            $priorities = explode(';', $this->configuration->get('PS_SPECIFIC_PRICE_PRIORITIES'));
        } catch (PrestaShopException $e) {
            throw new CoreException(
                'Something went wrong when trying to get default priorities of specific prices',
                0,
                $e->getPrevious()
            );
        }

        return new PriorityList($priorities);
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
        //@todo: filters are not fully handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'specific_price', 'sp')
            ->leftJoin(
                'sp',
                $this->dbPrefix . 'currency_lang', 'currency_lang',
                'sp.id_currency = currency_lang.id_currency AND currency_lang.id_lang = :langId'
            )
            ->leftJoin(
                'currency_lang',
                $this->dbPrefix . 'currency', 'currency',
                'currency.id_currency = currency_lang.id_currency'
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
            ->andWhere('sp.id_cart = 0')
            ->andWhere('sp.id_specific_price_rule = 0')
            ->orderBy('id_specific_price', 'asc')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('langId', $langId->getValue())
        ;

        if (!empty($filters['shopIds'])) {
            $qb->andWhere($qb->expr()->in('sp.id_shop', ':shopIds'))
                ->setParameter('shopIds', $filters['shopIds'], Connection::PARAM_INT_ARRAY)
            ;
        }

        return $qb;
    }

    /**
     * @param SpecificPrice $specificPrice
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertSpecificPriceIsUniquePerProduct(SpecificPrice $specificPrice): void
    {
        $productId = (int) $specificPrice->id_product;
        $combinationId = (int) $specificPrice->id_product_attribute;

        $alreadyExistingId = $this->findExisting(
            $productId,
            $combinationId,
            (int) $specificPrice->id_shop,
            (int) $specificPrice->id_group,
            (int) $specificPrice->id_country,
            (int) $specificPrice->id_currency,
            (int) $specificPrice->id_customer,
            (int) $specificPrice->from_quantity,
            $specificPrice->from,
            $specificPrice->to
        );

        // It is valid if its the same specific price that we are updating
        if ($alreadyExistingId && $alreadyExistingId->getValue() !== (int) $specificPrice->id) {
            throw new SpecificPriceConstraintException(
                sprintf(
                    'Identical specific price already exists for product "%d" and combination "%d',
                    $productId,
                    $combinationId
                ),
                SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT
            );
        }
    }
}
