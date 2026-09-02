<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountBuilder;
use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountConditionsUpdater;
use PrestaShop\PrestaShop\Adapter\Discount\Validate\DiscountValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\AddDiscountHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

#[AsCommandHandler]
class AddDiscountHandler implements AddDiscountHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly DiscountBuilder $discountBuilder,
        private readonly DiscountValidator $discountValidator,
        private readonly DiscountConditionsUpdater $discountConditionsUpdater,
        private readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    /**
     * @throws DiscountConstraintException
     */
    public function handle(AddDiscountCommand $command): DiscountId
    {
        $builtCartRule = $this->discountBuilder->build($command);
        $this->discountValidator->validateDiscountPropertiesForType($builtCartRule, $command->getProductConditions());
        // This should be tested by the validator but since both getters impact the same entity field and the validator is based on it
        // this is the only place where we can check this constraint
        if ($builtCartRule->getType() === DiscountType::PRODUCT_LEVEL && $command->getCheapestProduct() && null !== $command->getReductionProductId()) {
            throw new DiscountConstraintException('You need to choose only one target, cheapest, single product or product segment.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_TARGETS);
        }

        $this->discountValidator->validateAssociations(
            $command->getProductConditions(),
            $command->getCarrierIds() ? array_map(fn (CarrierId $carrierId) => $carrierId->getValue(), $command->getCarrierIds()) : null,
            $command->getCountryIds() ? array_map(fn (CountryId $countryId) => $countryId->getValue(), $command->getCountryIds()) : null,
            $command->getCustomerGroupIds() ? array_map(fn (GroupId $groupId) => $groupId->getValue(), $command->getCustomerGroupIds()) : null,
        );

        $discount = $this->discountRepository->add($builtCartRule);
        $newDiscountId = new DiscountId((int) $discount->id);

        $this->discountConditionsUpdater->update(
            $newDiscountId,
            $command->getProductConditions(),
            $command->getCarrierIds() ? array_map(fn (CarrierId $carrierId) => $carrierId->getValue(), $command->getCarrierIds()) : null,
            $command->getCountryIds() ? array_map(fn (CountryId $countryId) => $countryId->getValue(), $command->getCountryIds()) : null,
            $command->getCustomerGroupIds() ? array_map(fn (GroupId $groupId) => $groupId->getValue(), $command->getCustomerGroupIds()) : null,
        );
        $this->discountTypeRepository->setCompatibleTypesForDiscount($newDiscountId->getValue(), $command->getCompatibleDiscountTypeIds() ?? []);

        return $newDiscountId;
    }
}
