<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
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
