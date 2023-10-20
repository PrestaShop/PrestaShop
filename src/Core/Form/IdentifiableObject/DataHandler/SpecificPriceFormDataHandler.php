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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

class SpecificPriceFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(
        CommandBusInterface $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function create(array $data): int
    {
        $fixedPrice = isset($data['impact']['fixed_price_tax_excluded']) && !InitialPrice::isInitialPriceValue((string) $data['impact']['fixed_price_tax_excluded']) ?
            (string) $data['impact']['fixed_price_tax_excluded'] :
            InitialPrice::INITIAL_PRICE_VALUE
        ;

        $command = new AddSpecificPriceCommand(
            (int) $data['product_id'],
            $data['impact']['reduction']['type'] ?? Reduction::TYPE_AMOUNT,
            (string) ($data['impact']['reduction']['value'] ?? 0),
            (bool) $data['impact']['reduction']['include_tax'],
            $fixedPrice,
            (int) $data['from_quantity'],
            DateTime::buildNullableDateTime($data['date_range']['from']),
            DateTime::buildNullableDateTime($data['date_range']['to'])
        );

        $this->fillRelations($command, $data);

        /** @var SpecificPriceId $specificPriceId */
        $specificPriceId = $this->commandBus->handle($command);

        return $specificPriceId->getValue();
    }

    public function update($id, array $data): void
    {
        $command = new EditSpecificPriceCommand((int) $id);
        $this->fillRelations($command, $data);

        if (isset($data['from_quantity'])) {
            $command->setFromQuantity((int) $data['from_quantity']);
        }
        if (isset($data['date_range']) && array_key_exists('from', $data['date_range'])) {
            $command->setDateTimeFrom(DateTime::buildNullableDateTime($data['date_range']['from']));
        }
        if (isset($data['date_range']) && array_key_exists('to', $data['date_range'])) {
            $command->setDateTimeTo(DateTime::buildNullableDateTime($data['date_range']['to']));
        }

        // It switch input is true it means the price field is enabled
        if (!empty($data['impact']['disabling_switch_fixed_price_tax_excluded'])) {
            $command->setFixedPrice((string) $data['impact']['fixed_price_tax_excluded']);
        } else {
            $command->setFixedPrice(InitialPrice::INITIAL_PRICE_VALUE);
        }

        // It switch input is true it means the price field is enabled
        if (!empty($data['impact']['disabling_switch_reduction'])) {
            if (isset($data['impact']['reduction']['type'], $data['impact']['reduction']['value'])) {
                $command->setReduction((string) $data['impact']['reduction']['type'], (string) $data['impact']['reduction']['value']);
            }
            if (isset($data['impact']['reduction']['include_tax'])) {
                $command->setIncludesTax((bool) $data['impact']['reduction']['include_tax']);
            }
        } else {
            // When reduction is disabled we force its value to zero
            $command->setReduction(Reduction::TYPE_AMOUNT, '0');
        }

        $this->commandBus->handle($command);
    }

    /**
     * @param AddSpecificPriceCommand|EditSpecificPriceCommand $command
     * @param array<string, mixed> $data
     */
    private function fillRelations($command, array $data): void
    {
        if (isset($data['groups']['currency_id'])) {
            $command->setCurrencyId((int) $data['groups']['currency_id']);
        }
        if (isset($data['groups']['group_id'])) {
            $command->setGroupId((int) $data['groups']['group_id']);
        }
        if (array_key_exists('combination_id', $data)) {
            $command->setCombinationId((int) $data['combination_id']);
        }
        if (isset($data['groups']['country_id'])) {
            $command->setCountryId((int) $data['groups']['country_id']);
        }
        if (array_key_exists('shop_id', $data['groups'])) {
            $command->setShopId((int) $data['groups']['shop_id']);
        }
        if (isset($data['customer'])) {
            $command->setCustomerId($this->getCustomerId($data));
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return int
     */
    private function getCustomerId(array $data): int
    {
        $customerInput = $data['customer'];
        $firstItem = reset($customerInput);

        return isset($firstItem['id_customer']) ? (int) $firstItem['id_customer'] : 0;
    }
}
