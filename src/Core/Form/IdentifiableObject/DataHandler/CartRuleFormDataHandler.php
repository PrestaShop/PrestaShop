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
        $actionsData = $data['actions'];
        $actionsBuilder = new CartRuleActionBuilder();

        //@todo: not sure how freeShipping action works, it seems only one action can be done,
        //       but in form its not so obvious.
        //       Need to double check if ActionBuilder is wrong or some missing UX improvements in form
        if (isset($actionsData['reduction'])) {
            $reductionType = $actionsData['reduction']['type'];
            if ($reductionType === Reduction::TYPE_AMOUNT) {
                $actionsBuilder->setAmountDiscount(new MoneyAmountCondition(
                    new Money(
                        new DecimalNumber((string) $actionsData['reduction']['value']),
                        //@todo: hardcoded currencyId & shipping because the ReductioType doesn't fit 100%,
                        //       need to make some adjustments to include currency selection and shipping
                        new CurrencyId(1)
                    ),
                    !$actionsData['reduction']['include_tax'],
                    true
                ));
            } else {
                $actionsBuilder->setPercentageDiscount(new PercentageDiscount(
                    (float) $actionsData['reduction']['value'],
                    //@todo: also missing some fields for now so hardcoded false
                    false
                ));
            }
        }

        //@todo isn't the command missing customer selection?
        $this->commandBus->handle(new AddCartRuleCommand(
            $informationData['name'],
            isset($informationData['highlight']) ? (bool) $informationData['highlight'] : false,
            (bool) $informationData['partial_use'],
            (bool) $informationData['priority'],
            $informationData['active'],
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $informationData['valid_date_range']['from']),
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $informationData['valid_date_range']['to']),
            //@todo: from here its other tabs, not yet handled
            1,
            1,
            $actionsBuilder->build(),
            //@todo: minimum amount should probably also be object, because fields seems to depend on each other
            0.0,
            1,
            false,
            false
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
