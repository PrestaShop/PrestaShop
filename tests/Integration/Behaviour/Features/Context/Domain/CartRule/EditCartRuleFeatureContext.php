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

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class EditCartRuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * I edit cart rule :cartRuleReference with following data:
     *
     * @param TableNode $tableNode
     */
    public function editCartRule(string $cartRuleReference, TableNode $tableNode): void
    {
        $command = new EditCartRuleCommand($this->getSharedStorage()->get($cartRuleReference));
        $this->fillCommand($command, $this->localizeByRows($tableNode));
        $this->getCommandBus()->handle($command);
    }

    /**
     * @param EditCartRuleCommand $command
     * @param array<string, mixed> $data
     */
    private function fillCommand(EditCartRuleCommand $command, array $data): void
    {
        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (isset($data['highlight'])) {
            $command->setHighlightInCart(PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']));
        }
        if (isset($data['allow partial use'])) {
            $command->setAllowPartialUse(PrimitiveUtils::castStringBooleanIntoBoolean($data['allow partial use']));
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['code'])) {
            $command->setCode($data['code']);
        }
        if (isset($data['customer'])) {
            $command->setCustomerId($this->getSharedStorage()->get($data['customer']));
        }
        if (isset($data['priority'])) {
            $command->setPriority((int) $data['priority']);
        }
        if (isset($data['date range'])) {
            $this->setDateRange($data['date range'], $command);
        }
        if (isset($data['total quantity'])) {
            $command->setTotalQuantity((int) $data['total quantity']);
        }
        if (isset($data['quantity per user'])) {
            $command->setQuantityPerUser((int) $data['quantity per user']);
        }
        if (isset($data['minimum amount'])) {
            $command->setMinimumAmount(
                $data['minimum amount'],
                $this->getSharedStorage()->get($data['currency']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['tax included']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['shipping included'])
            );
        }
        if (isset($data['discount application type'])) {
            $command->setDiscountApplication(
                $data['discount application type'],
                // if specific product type is provided and product is not, then command should throw exception
                isset($data['discount product']) ? $this->getSharedStorage()->get($data['discount product']) : null
            );
        }

        if ($cartRuleAction = $this->buildAction($data)) {
            $command->setCartRuleAction($cartRuleAction);
        }
    }

    private function buildAction(array $data): ?CartRuleActionInterface
    {
        $builder = new CartRuleActionBuilder();

        if (isset($data['free shipping'])) {
            $builder->setFreeShipping(PrimitiveUtils::castStringBooleanIntoBoolean($data['free shipping']));
        }
        if (isset($data['percentage discount'])) {
            $builder->setPercentageDiscount(
                // @todo: string instead of float when related PR gets merged https://github.com/PrestaShop/PrestaShop/pull/31904
                new PercentageDiscount(
                    (float) $data['percentage discount'],
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['exclude discounted products'])
                )
            );
        }
        if (isset($data['amount discount'])) {
            $builder->setAmountDiscount(
                new AmountDiscountAction(
                    new MoneyAmountCondition(
                        new Money(
                            new DecimalNumber($data['amount discount']),
                            new CurrencyId($this->getSharedStorage()->get($data['currency']))
                        ),
                        //@todo: after PR is merged, it should be tax included (not excluded) https://github.com/PrestaShop/PrestaShop/pull/31904
                        $data['tax included'],
                        false
                    )
                )
            );
        }
    }

    private function setDateRange(string $dateRange, EditCartRuleCommand $command): void
    {
        $rangeParts = explode(',', $dateRange);
        if (!isset($rangeParts[0], $rangeParts[1])) {
            throw new RuntimeException('Expected date range format: "from: Y-m-d H:i:s, to: Y-m-d H:i:s"');
        }

        $command->setValidDateRange(
            new DateTimeImmutable(str_replace('from:', '', $rangeParts[0])),
            new DateTimeImmutable(str_replace('to:', '', $rangeParts[1]))
        );
    }
}
