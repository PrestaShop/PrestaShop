<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SpecificPrice\AbstractSpecificPriceHandler;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\CommandHandler\AddSpecificPriceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShopException;
use SpecificPrice;

/**
 * Handles AddSpecificPriceCommand using legacy object model
 */
final class AddSpecificPriceHandler extends AbstractSpecificPriceHandler implements AddSpecificPriceHandlerInterface
{
    /**
     * @param AddSpecificPriceCommand $command
     *
     * @return SpecificPriceId
     *
     * @throws SpecificPriceConstraintException
     * @throws SpecificPriceException
     */
    public function handle(AddSpecificPriceCommand $command): SpecificPriceId
    {
        try {
            $specificPrice = $this->createSpecificPriceFromCommand($command);

            if (false === $specificPrice->validateFields(false)) {
                throw new SpecificPriceConstraintException('Specific price contains invalid field values');
            }

            if (!$specificPrice->add()) {
                throw new SpecificPriceException('Failed to add new specific price');
            }
        } catch (PrestaShopException $e) {
            throw new SpecificPriceException('An error occurred when trying to add new specific price');
        }

        return new SpecificPriceId($specificPrice->id);
    }

    /**
     * Creates legacy SpecificPrice object from command
     *
     * @param AddSpecificPriceCommand $command
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopException
     * @throws SpecificPriceConstraintException
     */
    private function createSpecificPriceFromCommand(AddSpecificPriceCommand $command): SpecificPrice
    {
        $specificPrice = new SpecificPrice();

        $specificPrice->id_product = $command->getProductId()->getValue();
        $specificPrice->reduction_type = $command->getReduction()->getType();
        $specificPrice->reduction = $command->getReduction()->getValue();
        $specificPrice->reduction_tax = $command->isIncludeTax();
        $specificPrice->price = $command->getPrice();
        $specificPrice->from_quantity = $command->getFromQuantity();
        $specificPrice->id_shop_group = $command->getShopGroupId() ?? 0;
        $specificPrice->id_shop = $command->getShopId() ?? 0;
        $specificPrice->id_cart = $command->getCartId() ?? 0;
        $specificPrice->id_product_attribute = $command->getProductAttributeId() ?? 0;
        $specificPrice->id_currency = $command->getCurrencyId() ?? 0;
        $specificPrice->id_specific_price_rule = $command->getCartRuleId() ?? 0;
        $specificPrice->id_country = $command->getCountryId() ?? 0;
        $specificPrice->id_group = $command->getGroupId() ?? 0;
        $specificPrice->id_customer = $command->getCustomerId() ?? 0;
        $specificPrice->from = DateTime::NULL_VALUE;
        $specificPrice->to = DateTime::NULL_VALUE;

        $from = $command->getDateTimeFrom();
        $to = $command->getDateTimeTo();

        if ($from && $to) {
            $this->assertDateRangeIsNotInverse($from, $to);
        }

        if ($from) {
            $specificPrice->from = $from->format('Y-m-d H:i:s');
        }

        if ($to) {
            $specificPrice->to = $to->format('Y-m-d H:i:s');
        }

        return $specificPrice;
    }
}
