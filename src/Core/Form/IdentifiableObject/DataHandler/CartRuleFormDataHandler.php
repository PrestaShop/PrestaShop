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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class CartRuleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    public function __construct(
        CommandBusInterface $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        $informationData = $data['information'];
        $conditionsData = $data['conditions'];
        $dateRange = $conditionsData['valid_date_range'];

        $command = new AddCartRuleCommand(
            $informationData['name'],
            isset($informationData['highlight']) && (bool) $informationData['highlight'],
            (bool) $informationData['partial_use'],
            (int) $informationData['priority'],
            (bool) $informationData['active'],
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['from']),
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['to']),
            (int) $conditionsData['total_available'],
            (int) $conditionsData['available_per_user'],
            $this->buildCartRuleActionForCreate($data['actions'])
        );

        if (!empty($conditionsData['minimum_amount']['amount'])) {
            $amountData = $conditionsData['minimum_amount'];
            $command->setMinimumAmountCondition(
                $amountData['amount'],
                (int) $amountData['currency'],
                (bool) $amountData['tax_included'],
                (bool) $amountData['shipping_included']
            );
        }

        $this->commandBus->handle($command);
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }

    private function buildCartRuleActionForCreate(array $actionsData): CartRuleActionInterface
    {
        $actionBuilder = new CartRuleActionBuilder();

        if (!empty($actionsData['reduction']['value'])) {
            $reductionType = $actionsData['reduction']['type'];
            if ($reductionType === Reduction::TYPE_AMOUNT) {
                $actionBuilder->setAmountDiscount(new MoneyAmountCondition(
                    new Money(
                        new DecimalNumber((string) $actionsData['reduction']['value']),
                        //@todo: hardcoded currencyId & shipping because the ReductioType doesn't fit 100%,
                        //       need to make some adjustments to include currency selection and shipping
                        new CurrencyId(1)
                    ),
                    $actionsData['reduction']['include_tax'],
                    true
                ));
            } else {
                $actionBuilder->setPercentageDiscount(new PercentageDiscount(
                    //@todo: use string and DecimalNumber inside PercentageDiscount instead of float
                    (float) $actionsData['reduction']['value'],
                    //@todo: also missing some fields for now so hardcoded false
                    false
                ));
            }
        }

        $actionBuilder->setFreeShipping((bool) $actionsData['free_shipping']);

        if (!empty($actionsData['gift_product'])) {
            $actionBuilder->setGiftProduct(new GiftProduct(
                $actionsData['gift_product']['product_id'],
                $actionsData['gift_product']['combination_id'] ?? null
            ));
        }

        return $actionBuilder->build();
    }
}
