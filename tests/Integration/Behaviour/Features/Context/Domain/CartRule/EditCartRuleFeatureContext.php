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
use CartRule;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
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
     * @Then cart rule :cartRuleReference should have the following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $tableNode
     */
    public function assertCartRuleProperties(string $cartRuleReference, TableNode $tableNode): void
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getSharedStorage()->get($cartRuleReference);
        /** @var EditableCartRule $editableCartRule */
        $editableCartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing((int) $cartRule->id));

        $data = $this->localizeByRows($tableNode);
        $information = $editableCartRule->getInformation();
        $conditions = $editableCartRule->getConditions();
        $actions = $editableCartRule->getActions();

        if (isset($data['name'])) {
            Assert::assertSame($data['name'], $information->getLocalizedNames(), 'Unexpected localized name');
        }
        if (isset($data['description'])) {
            Assert::assertSame($data['description'], $information->getDescription(), 'Unexpected description');
        }
        if (isset($data['highlight'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']),
                $information->isHighlight(),
                'Unexpected highlight'
            );
        }
        if (isset($data['allow partial use'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['allow partial use']),
                $information->isPartialUse(),
                'Unexpected partial use'
            );
        }
        if (isset($data['enabled'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
                $information->isEnabled(),
                'Unexpected enabled property'
            );
        }
        if (isset($data['code'])) {
            Assert::assertSame($data['code'], $information->getCode(), 'Unexpected code');
        }
        if (isset($data['customer'])) {
            Assert::assertSame(
                $this->getSharedStorage()->get($data['customer']),
                $conditions->getCustomerId()->getValue(),
                'Unexpected customer id'
            );
        }
        if (isset($data['priority'])) {
            Assert::assertSame((int) $data['priority'], $information->getPriority(), 'Unexpected priority');
        }
        if (isset($data['date from'])) {
            Assert::assertEquals(
                $data['date from'],
                $conditions->getDateFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'Unexpected date from'
            );
        }
        if (isset($data['date to'])) {
            Assert::assertEquals(
                $data['date to'],
                $conditions->getDateTo()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'Unexpected date to'
            );
        }
        if (isset($data['total quantity'])) {
            Assert::assertSame((int) $data['total quantity'], $conditions->getQuantity(), 'Unexpected quantity');
        }
        if (isset($data['quantity per user'])) {
            Assert::assertSame((int) $data['quantity per user'], $conditions->getQuantityPerUser(), 'Unexpected quantity per user');
        }

        if (isset($data['minimum amount'])) {
            $minimum = $conditions->getMinimum();
            Assert::assertEquals($data['minimum amount'], $minimum->getAmount(), 'Unexpected minimum amount');
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['tax included']),
                $minimum->isAmountTax(),
                'Unexpected tax included property'
            );

            Assert::assertSame(
                $this->getSharedStorage()->get($data['currency']),
                $minimum->getCurrencyId()->getValue(),
                'Unexpected currency'
            );
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['shipping included']),
                $minimum->isShipping(),
                'Unexpected shipping included property'
            );
        }

        if (isset($data['discount application type'])) {
            $expectedDiscountApplicationType = $data['discount application type'];
            Assert::assertSame(
                $expectedDiscountApplicationType,
                $actions->getDiscountApplicationType(),
                'Unexpected discount application type'
            );

            if ($expectedDiscountApplicationType === DiscountApplicationType::SPECIFIC_PRODUCT) {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['discount product']),
                    $actions->getReduction()->getProductId() ? $actions->getReduction()->getProductId()->getValue() : null,
                    'Unexpected discount product'
                );
            }

            if (isset($data['gift product'])) {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['gift product']),
                    $actions->getGiftProductId() ? $actions->getGiftProductId()->getValue() : null,
                    'Unexpected gift product'
                );
            }

            if (isset($data['percentage discount'])) {
                Assert::assertTrue(
                    $actions->getReduction()->getPercent()->equals(new DecimalNumber($data['percentage discount'])),
                    'Unexpected percentage discount'
                );
            }

            if (isset($data['amount discount'])) {
                Assert::assertTrue(
                    $actions->getReduction()->getAmount()->equals(new DecimalNumber($data['amount discount'])),
                    'Unexpected amount discount'
                );
            }

            if (isset($data['currency'])) {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['currency']),
                    $actions->getReduction()->getCurrencyId() ? $actions->getReduction()->getCurrencyId()->getValue() : null,
                    'Unexpected currency'
                );
            }

            if (isset($data['free shipping'])) {
                Assert::assertSame(
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['free shipping']),
                    $actions->isFreeShipping(),
                    'Unexpected free shipping'
                );
            }

            if (isset($data['gift product'])) {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['gift product']),
                    $actions->getGiftProductId() ? $actions->getGiftProductId()->getValue() : null,
                    'Unexpected gift product'
                );
            }
            if (isset($data['gift combination'])) {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['gift combination']),
                    $actions->getGiftCombinationId() ? $actions->getGiftCombinationId()->getValue() : null,
                    'Unexpected gift combination'
                );
            }
        }
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
        if (isset($data['enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
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
        $actionWasSet = false;
        $builder = new CartRuleActionBuilder();

        if (isset($data['free shipping'])) {
            $actionWasSet = true;
            $builder->setFreeShipping(PrimitiveUtils::castStringBooleanIntoBoolean($data['free shipping']));
        }
        if (isset($data['percentage discount'])) {
            $actionWasSet = true;
            $builder->setPercentageDiscount(
                // @todo: string instead of float when related PR gets merged https://github.com/PrestaShop/PrestaShop/pull/31904
                new PercentageDiscount(
                    (float) $data['percentage discount'],
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['exclude discounted products'])
                )
            );
        }
        if (isset($data['amount discount'])) {
            $actionWasSet = true;
            $builder->setAmountDiscount(
                new MoneyAmountCondition(
                    new Money(
                        new DecimalNumber($data['amount discount']),
                        new CurrencyId($this->getSharedStorage()->get($data['currency']))
                    ),
                    //@todo: after PR is merged, it should be tax included (not excluded) https://github.com/PrestaShop/PrestaShop/pull/31904
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['tax included']),
                    false
                )
            );
        }

        if (!$actionWasSet) {
            return null;
        }

        return $builder->build();
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
