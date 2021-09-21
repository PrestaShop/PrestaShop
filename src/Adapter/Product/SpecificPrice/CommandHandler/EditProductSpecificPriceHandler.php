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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditProductSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\EditProductSpecificPriceHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use SpecificPrice;

/**
 * Handles @see EditProductSpecificPriceCommand using legacy object model
 */
class EditProductSpecificPriceHandler implements EditProductSpecificPriceHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(
        SpecificPriceRepository $specificPriceRepository
    ) {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(EditProductSpecificPriceCommand $command): void
    {
        $specificPrice = $this->specificPriceRepository->get($command->getSpecificPriceId());

        $this->specificPriceRepository->partialUpdate(
            $specificPrice,
            $this->fillUpdatableProperties($command, $specificPrice)
        );
    }

    /**
     * @param EditProductSpecificPriceCommand $command
     * @param SpecificPrice $specificPrice
     *
     * @return string[]
     */
    private function fillUpdatableProperties(EditProductSpecificPriceCommand $command, SpecificPrice $specificPrice): array
    {
        $updatableProperties = [];
        if (null !== $command->getReduction()) {
            $specificPrice->reduction_type = $command->getReduction()->getType();
            $specificPrice->reduction = (string) $command->getReduction()->getValue();
            $updatableProperties = [
                'reduction_type',
                'reduction',
            ];
        }

        if (null !== $command->includesTax()) {
            $specificPrice->reduction_tax = $command->includesTax();
            $updatableProperties[] = 'reduction_tax';
        }

        if (null !== $command->getPrice()) {
            $specificPrice->price = (float) (string) $command->getPrice();
            $updatableProperties[] = 'price';
        }

        if (null !== $command->getFromQuantity()) {
            $specificPrice->from_quantity = $command->getFromQuantity();
            $updatableProperties[] = 'from_quantity';
        }

        if (null !== $command->getShopId()) {
            $specificPrice->id_shop = $command->getShopId()->getValue();
            $updatableProperties[] = 'id_shop';
        }

        if (null !== $command->getCombinationId()) {
            $specificPrice->id_product_attribute = $command->getCombinationId()->getValue();
            $updatableProperties[] = 'id_product_attribute';
        }

        if (null !== $command->getCurrencyId()) {
            $specificPrice->id_currency = $command->getCurrencyId()->getValue();
            $updatableProperties[] = 'id_currency';
        }

        if (null !== $command->getCountryId()) {
            $specificPrice->id_country = $command->getCountryId()->getValue();
            $updatableProperties[] = 'id_country';
        }

        if (null !== $command->getGroupId()) {
            $specificPrice->id_group = $command->getGroupId()->getValue();
            $updatableProperties[] = 'id_group';
        }

        if (null !== $command->getCustomerId()) {
            $specificPrice->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }

        if (null !== $command->getDateTimeFrom()) {
            $specificPrice->from = $command->getDateTimeFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'from';
        }

        if (null !== $command->getDateTimeTo()) {
            $specificPrice->to = $command->getDateTimeTo()->format(DateTime::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'to';
        }

        return $updatableProperties;
    }
}
