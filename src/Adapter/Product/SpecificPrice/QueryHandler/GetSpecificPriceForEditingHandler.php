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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler\GetSpecificPriceForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\CustomerInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use SpecificPrice;

/**
 * Handles @see GetSpecificPriceForEditing using legacy object model
 */
class GetSpecificPriceForEditingHandler implements GetSpecificPriceForEditingHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        SpecificPriceRepository $specificPriceRepository,
        CustomerRepository $customerRepository
    ) {
        $this->specificPriceRepository = $specificPriceRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSpecificPriceForEditing $query): SpecificPriceForEditing
    {
        $specificPrice = $this->specificPriceRepository->get($query->getSpecificPriceId());
        $fixedPrice = InitialPrice::isInitialPriceValue($specificPrice->price) ?
            new InitialPrice() :
            new FixedPrice($specificPrice->price)
        ;

        // VO stores percent expressed based on 100, while the DB stored the float value (VO: 57.5 - DB: 0.575)
        $reductionValue = new DecimalNumber((string) $specificPrice->reduction);
        if ($specificPrice->reduction_type === Reduction::TYPE_PERCENTAGE) {
            $reductionValue = $reductionValue->times(new DecimalNumber('100'));
        }

        return new SpecificPriceForEditing(
            (int) $specificPrice->id,
            $specificPrice->reduction_type,
            $reductionValue,
            (bool) $specificPrice->reduction_tax,
            $fixedPrice,
            (int) $specificPrice->from_quantity,
            DateTimeUtil::buildNullableDateTime($specificPrice->from),
            DateTimeUtil::buildNullableDateTime($specificPrice->to),
            (int) $specificPrice->id_product,
            $this->getCustomerInfo($specificPrice),
            (int) $specificPrice->id_product_attribute ?: null,
            (int) $specificPrice->id_shop ?: null,
            (int) $specificPrice->id_currency ?: null,
            (int) $specificPrice->id_country ?: null,
            (int) $specificPrice->id_group ?: null
        );
    }

    /**
     * @param SpecificPrice $specificPrice
     *
     * @return CustomerInfo|null
     */
    private function getCustomerInfo(SpecificPrice $specificPrice): ?CustomerInfo
    {
        $customerIdValue = (int) $specificPrice->id_customer;

        if (!$customerIdValue) {
            return null;
        }

        $customer = $this->customerRepository->get(new CustomerId($customerIdValue));

        return new CustomerInfo(
            $customerIdValue,
            $customer->firstname,
            $customer->lastname,
            $customer->email
        );
    }
}
