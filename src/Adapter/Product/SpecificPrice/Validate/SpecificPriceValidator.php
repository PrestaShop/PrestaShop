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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Validate;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\NoCountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use SpecificPrice;

/**
 * Validates SpecificPrice properties using legacy object model
 */
class SpecificPriceValidator extends AbstractObjectModelValidator
{
    /**
     * @var ShopGroupRepository
     */
    private $shopGroupRepository;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @param ShopGroupRepository $shopGroupRepository
     * @param ShopRepository $shopRepository
     * @param CombinationRepository $combinationRepository
     * @param CurrencyRepository $currencyRepository
     * @param CountryRepository $countryRepository
     * @param GroupRepository $groupRepository
     * @param CustomerRepository $customerRepository
     * @param ProductRepository $productRepository
     * @param NumberExtractor $numberExtractor
     */
    public function __construct(
        ShopGroupRepository $shopGroupRepository,
        ShopRepository $shopRepository,
        CombinationRepository $combinationRepository,
        CurrencyRepository $currencyRepository,
        CountryRepository $countryRepository,
        GroupRepository $groupRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        NumberExtractor $numberExtractor
    ) {
        $this->shopGroupRepository = $shopGroupRepository;
        $this->shopRepository = $shopRepository;
        $this->combinationRepository = $combinationRepository;
        $this->currencyRepository = $currencyRepository;
        $this->countryRepository = $countryRepository;
        $this->groupRepository = $groupRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->numberExtractor = $numberExtractor;
    }

    /**
     * @param SpecificPrice $specificPrice
     *
     * @throws CoreException
     * @throws SpecificPriceConstraintException
     */
    public function validate(SpecificPrice $specificPrice): void
    {
        $this->validateSpecificPriceProperty($specificPrice, 'price', SpecificPriceConstraintException::INVALID_FIXED_PRICE);
        $this->validateSpecificPriceProperty($specificPrice, 'reduction', SpecificPriceConstraintException::INVALID_REDUCTION_AMOUNT);
        $this->validateSpecificPriceProperty($specificPrice, 'reduction_tax', SpecificPriceConstraintException::INVALID_FROM_QUANTITY);
        $this->validateSpecificPriceProperty($specificPrice, 'reduction_type', SpecificPriceConstraintException::INVALID_REDUCTION_TYPE);
        $this->validateSpecificPriceProperty($specificPrice, 'from_quantity', SpecificPriceConstraintException::INVALID_FROM_QUANTITY);
        $this->validateSpecificPriceProperty($specificPrice, 'from', SpecificPriceConstraintException::INVALID_FROM_DATETIME);
        $this->validateSpecificPriceProperty($specificPrice, 'to', SpecificPriceConstraintException::INVALID_TO_DATETIME);
        $this->assertReductionOrFixedPriceIsProvided($specificPrice);
        $this->assertDateRangeIsNotInverse($specificPrice);

        $this->validateSpecificPriceProperty($specificPrice, 'id_cart', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_country', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_currency', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_customer', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_group', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_product', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_product_attribute', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_shop', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_shop_group', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->validateSpecificPriceProperty($specificPrice, 'id_specific_price_rule', SpecificPriceConstraintException::INVALID_RELATION_ID);
        $this->assertRelatedEntitiesExist($specificPrice);
    }

    /**
     * @param SpecificPrice $specificPrice
     * @param string $property
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws SpecificPriceConstraintException
     */
    private function validateSpecificPriceProperty(SpecificPrice $specificPrice, string $property, int $errorCode): void
    {
        $this->validateObjectModelProperty($specificPrice, $property, SpecificPriceConstraintException::class, $errorCode);
    }

    /**
     * Checks if date range values are not inverse. (range from not bigger than range to)
     *
     * @param SpecificPrice $specificPrice
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertDateRangeIsNotInverse(SpecificPrice $specificPrice)
    {
        if (empty($specificPrice->from) || empty($specificPrice->to)) {
            return;
        }

        if (DateTimeUtil::isNull($specificPrice->from) || DateTimeUtil::isNull($specificPrice->to)) {
            return;
        }

        $from = new DateTime($specificPrice->from);
        $to = new DateTime($specificPrice->to);
        if ($from->diff($to)->invert) {
            throw new SpecificPriceConstraintException('The date time for specific price cannot be inverse', SpecificPriceConstraintException::INVALID_DATE_RANGE);
        }
    }

    /**
     * @param SpecificPrice $specificPrice
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertReductionOrFixedPriceIsProvided(SpecificPrice $specificPrice): void
    {
        $reduction = new DecimalNumber('0');
        $price = new DecimalNumber('0');

        if (null !== $specificPrice->reduction) {
            $reduction = $this->numberExtractor->extract($specificPrice, 'reduction');
        }
        if (null !== $specificPrice->price) {
            $price = $this->numberExtractor->extract($specificPrice, 'price');
        }

        if ($reduction->equalsZero() && $price->equalsZero()) {
            throw new SpecificPriceConstraintException(
                'Specific price reduction or price must be set',
                SpecificPriceConstraintException::REDUCTION_OR_PRICE_MUST_BE_SET
            );
        }
    }

    /**
     * @param SpecificPrice $specificPrice
     */
    private function assertRelatedEntitiesExist(SpecificPrice $specificPrice): void
    {
        $productId = (int) $specificPrice->id_product;
        $this->productRepository->assertProductExists(new ProductId($productId));

        $shopGroupId = (int) $specificPrice->id_shop_group;
        if ($shopGroupId) {
            $this->shopGroupRepository->assertShopGroupExists(new ShopGroupId($shopGroupId));
        }

        $shopId = (int) $specificPrice->id_shop;
        if ($shopId !== NoShopId::NO_SHOP_ID) {
            $this->shopRepository->assertShopExists(new ShopId($shopId));
        }

        $combinationId = (int) $specificPrice->id_product_attribute;
        if ($combinationId !== NoCombinationId::NO_COMBINATION_ID) {
            $this->combinationRepository->assertCombinationExists(new CombinationId($combinationId));
        }

        $currencyId = (int) $specificPrice->id_currency;
        if ($currencyId !== NoCurrencyId::NO_CURRENCY_ID) {
            $this->currencyRepository->assertCurrencyExists(new CurrencyId($currencyId));
        }

        $countryId = (int) $specificPrice->id_country;
        if ($countryId !== null && $countryId !== NoCountryId::NO_COUNTRY_ID_VALUE) {
            $this->countryRepository->assertCountryExists(new CountryId($countryId));
        }

        $groupId = (int) $specificPrice->id_group;
        if ($groupId !== NoGroupId::NO_GROUP_ID) {
            $this->groupRepository->assertGroupExists(new GroupId($groupId));
        }

        $customerId = (int) $specificPrice->id_customer;
        if ($customerId) {
            $this->customerRepository->assertCustomerExists(new CustomerId($customerId));
        }
    }
}
