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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class EditCartRuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I edit cart rule :cartRuleReference with following properties:
     *
     * @param TableNode $tableNode
     */
    public function editCartRule(string $cartRuleReference, TableNode $tableNode): void
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getSharedStorage()->get($cartRuleReference);

        try {
            $command = new EditCartRuleCommand($cartRule->id);
            $this->fillCommand($command, $this->localizeByRows($tableNode));
            $this->getCommandBus()->handle($command);
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
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
        if (isset($data['allow_partial_use'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']),
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
                !empty($data['customer']) ? $this->getSharedStorage()->get($data['customer']) : 0,
                $conditions->getCustomerId()->getValue(),
                'Unexpected customer id'
            );
        }
        if (isset($data['priority'])) {
            Assert::assertSame((int) $data['priority'], $information->getPriority(), 'Unexpected priority');
        }
        if (isset($data['valid_from'])) {
            Assert::assertEquals(
                $data['valid_from'],
                $conditions->getDateFrom()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_from'
            );
        }
        if (isset($data['valid_to'])) {
            Assert::assertEquals(
                $data['valid_to'],
                $conditions->getDateTo()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_to'
            );
        }
        if (isset($data['total quantity'])) {
            Assert::assertSame((int) $data['total quantity'], $conditions->getQuantity(), 'Unexpected quantity');
        }
        if (isset($data['quantity per user'])) {
            Assert::assertSame((int) $data['quantity per user'], $conditions->getQuantityPerUser(), 'Unexpected quantity per user');
        }

        if (isset($data['minimum_amount'])) {
            if (empty($data['minimum_amount'])) {
                Assert::assertNull($conditions->getMinimum(), 'unexpected minimum_amount');
            } else {
                $minimum = $conditions->getMinimum();
                Assert::assertTrue(
                    $minimum->getAmount()->equals(new DecimalNumber($data['minimum_amount'])),
                    'Unexpected minimum_amount'
                );

                Assert::assertSame(
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                    $minimum->isAmountTax(),
                    'Unexpected minimum_amount_tax_included'
                );

                Assert::assertSame(
                    $this->getSharedStorage()->get($data['minimum_amount_currency']),
                    $minimum->getCurrencyId(),
                    'Unexpected minimum_amount_currency'
                );

                Assert::assertSame(
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']),
                    $minimum->isShipping(),
                    'Unexpected minimum_amount_shipping_included'
                );
            }
        }

        if (isset($data['discount_application_type'])) {
            $expectedDiscountApplicationType = $data['discount_application_type'];
            Assert::assertSame(
                $expectedDiscountApplicationType,
                $actions->getDiscountApplicationType(),
                'Unexpected discount_application_type'
            );

            Assert::assertSame(
                !empty($data['discount_product']) ? $this->getSharedStorage()->get($data['discount_product']) : null,
                $actions->getReduction()->getProductId(),
                'Unexpected discount_product'
            );
        }

        if (isset($data['reduction_percentage'])) {
            Assert::assertTrue(
                $actions->getReduction()->getPercent()->equals(new DecimalNumber($data['reduction_percentage'])),
                'Unexpected reduction_percentage'
            );
        }

        if (isset($data['reduction_amount'])) {
            Assert::assertTrue(
                $actions->getReduction()->getAmount()->equals(new DecimalNumber($data['reduction_amount'])),
                'Unexpected reduction_amount'
            );
        }

        if (isset($data['reduction_currency'])) {
            if ($data['reduction_currency'] === '') {
                Assert::assertNull(
                    $actions->getReduction()->getCurrencyId(),
                    'Unexpected reduction_currency'
                );
            } else {
                Assert::assertSame(
                    $this->getSharedStorage()->get($data['reduction_currency']),
                    $actions->getReduction()->getCurrencyId(),
                    'Unexpected reduction_currency'
                );
            }
        }

        if (isset($data['reduction_apply_to_discounted_products'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_apply_to_discounted_products']),
                $actions->getReduction()->applyToDiscountedProducts(),
                'Unexpected reduction_apply_to_discounted_products'
            );
        }

        if (isset($data['free_shipping'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']),
                $actions->isFreeShipping(),
                'Unexpected free_shipping'
            );
        }

        if (isset($data['gift_product'])) {
            Assert::assertSame(
                $this->getSharedStorage()->get($data['gift_product']),
                $actions->getGiftProductId(),
                'Unexpected gift_product'
            );
        }

        if (isset($data['gift_combination'])) {
            Assert::assertSame(
                !empty($data['gift_combination']) ? $this->getSharedStorage()->get($data['gift_combination']) : null,
                $actions->getGiftCombinationId(),
                'Unexpected gift_combination'
            );
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
        if (isset($data['allow_partial_use'])) {
            $command->setAllowPartialUse(PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']));
        }
        if (isset($data['enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
        }
        if (isset($data['code'])) {
            $command->setCode($data['code']);
        }
        if (isset($data['customer'])) {
            $command->setCustomerId(!empty($data['customer']) ? $this->getSharedStorage()->get($data['customer']) : 0);
        }
        if (isset($data['priority'])) {
            $command->setPriority((int) $data['priority']);
        }
        if (isset($data['valid_from'])) {
            $command->setDateRange(
                new DateTimeImmutable($data['valid_from']),
                new DateTimeImmutable($data['valid_to'])
            );
        }
        if (isset($data['total_quantity'])) {
            $command->setTotalQuantity((int) $data['total_quantity']);
        }
        if (isset($data['quantity_per_user'])) {
            $command->setQuantityPerUser((int) $data['quantity_per_user']);
        }
        if (isset($data['minimum_amount'])) {
            $command->setMinimumAmount(
                $data['minimum_amount'],
                $this->getSharedStorage()->get($data['minimum_amount_currency']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included'])
            );
        }

        if (isset($data['discount_application_type'])) {
            $command->setDiscountApplication(
                $data['discount_application_type'],
                // if specific product type is provided and product is not, then command should throw exception
                isset($data['discount_product']) ? $this->getSharedStorage()->get($data['discount_product']) : null
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

        if (isset($data['free_shipping'])) {
            $actionWasSet = true;
            $builder->setFreeShipping(PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']));
        }
        if (isset($data['gift_product'])) {
            $actionWasSet = true;
            $builder->setGiftProduct(
                $this->getSharedStorage()->get($data['gift_product']),
                isset($data['gift_combination']) ? $this->getSharedStorage()->get($data['gift_combination']) : null
            );
        }
        if (isset($data['reduction_percentage'])) {
            $actionWasSet = true;
            $builder->setPercentageDiscount(
                $data['reduction_percentage'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_apply_to_discounted_products'])
            );
        }
        if (isset($data['reduction_amount'])) {
            $actionWasSet = true;
            $builder->setAmountDiscount(
                $data['reduction_amount'],
                $this->getSharedStorage()->get($data['reduction_currency']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_tax'])
            );
        }

        if (!$actionWasSet) {
            return null;
        }

        return $builder->build();
    }
}
