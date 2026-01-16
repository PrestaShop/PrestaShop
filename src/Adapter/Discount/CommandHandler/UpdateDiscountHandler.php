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

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountConditionsUpdater;
use PrestaShop\PrestaShop\Adapter\Discount\Update\Filler\DiscountFiller;
use PrestaShop\PrestaShop\Adapter\Discount\Validate\DiscountValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\UpdateDiscountCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotUpdateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

#[AsCommandHandler]
class UpdateDiscountHandler implements UpdateDiscountCommandHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly DiscountFiller $discountFiller,
        private readonly DiscountValidator $discountValidator,
        private readonly DiscountConditionsUpdater $discountConditionsUpdater,
        private readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    public function handle(UpdateDiscountCommand $command): void
    {
        $cartRule = $this->discountRepository->get($command->getDiscountId());
        $updatableProperties = $this->discountFiller->fillUpdatableProperties($cartRule, $command);
        // This should be tested by the validator but since both getters impact the same entity field and the validator is based on it
        // this is the only place where we can check this constraint
        if ($cartRule->getType() === DiscountType::PRODUCT_LEVEL && $command->getCheapestProduct() && null !== $command->getReductionProductId()) {
            throw new DiscountConstraintException('You need to choose only one target, cheapest, single product or product segment.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_TARGETS);
        }
        $this->discountValidator->validateDiscountPropertiesForType($cartRule, $command->getProductConditions());
        $this->discountValidator->validateAssociations(
            $command->getProductConditions(),
            $command->getCarrierIds() ? array_map(fn (CarrierId $carrierId) => $carrierId->getValue(), $command->getCarrierIds()) : null,
            $command->getCountryIds() ? array_map(fn (CountryId $countryId) => $countryId->getValue(), $command->getCountryIds()) : null,
            $command->getCustomerGroupIds() ? array_map(fn (GroupId $groupId) => $groupId->getValue(), $command->getCustomerGroupIds()) : null,
        );

        $this->discountRepository->partialUpdate(
            $cartRule,
            $updatableProperties,
            CannotUpdateDiscountException::FAILED_UPDATE_DISCOUNT
        );

        $this->discountConditionsUpdater->update(
            $command->getDiscountId(),
            // If cheapest product or single product is set we remove the product conditions (using empty array)
            $command->getCheapestProduct() || null !== $command->getReductionProductId() ? [] : $command->getProductConditions(),
            $command->getCarrierIds() !== null ? array_map(fn (CarrierId $carrierId) => $carrierId->getValue(), $command->getCarrierIds()) : null,
            $command->getCountryIds() !== null ? array_map(fn (CountryId $countryId) => $countryId->getValue(), $command->getCountryIds()) : null,
            $command->getCustomerGroupIds() !== null ? array_map(fn (GroupId $groupId) => $groupId->getValue(), $command->getCustomerGroupIds()) : null,
        );

        if (null !== $command->getCompatibleDiscountTypeIds()) {
            $this->discountTypeRepository->setCompatibleTypesForDiscount($command->getDiscountId()->getValue(), $command->getCompatibleDiscountTypeIds());
        }
    }
}
