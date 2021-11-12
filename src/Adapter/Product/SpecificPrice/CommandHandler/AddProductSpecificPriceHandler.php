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
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddProductSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\AddProductSpecificPriceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopException;
use SpecificPrice;

/**
 * Handles AddProductSpecificPriceCommand using legacy object model
 */
final class AddProductSpecificPriceHandler implements AddProductSpecificPriceHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(SpecificPriceRepository $specificPriceRepository)
    {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductSpecificPriceCommand $command): SpecificPriceId
    {
        $specificPrice = $this->createSpecificPriceFromCommand($command);

        return $this->specificPriceRepository->add($specificPrice);
    }

    /**
     * Creates legacy SpecificPrice object from command
     *
     * @param AddProductSpecificPriceCommand $command
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopException
     * @throws SpecificPriceConstraintException
     */
    private function createSpecificPriceFromCommand(AddProductSpecificPriceCommand $command): SpecificPrice
    {
        $specificPrice = new SpecificPrice();

        $specificPrice->id_product = $command->getProductId()->getValue();
        $specificPrice->reduction_type = $command->getReduction()->getType();
        $specificPrice->reduction = $command->getReduction()->getValue();
        $specificPrice->reduction_tax = $command->includesTax();
        $specificPrice->price = $command->getPrice();
        $specificPrice->from_quantity = $command->getFromQuantity();
        $specificPrice->id_shop = $command->getShopId() ?? 0;
        $specificPrice->id_product_attribute = null !== $command->getCombinationId() ? $command->getCombinationId()->getValue() : 0;
        $specificPrice->id_currency = $command->getCurrencyId() ?? 0;
        $specificPrice->id_country = $command->getCountryId() ?? 0;
        $specificPrice->id_group = $command->getGroupId() ?? 0;
        $specificPrice->id_customer = $command->getCustomerId() ?? 0;
        $specificPrice->from = $command->getDateTimeFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $specificPrice->to = $command->getDateTimeTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);

        return $specificPrice;
    }
}
