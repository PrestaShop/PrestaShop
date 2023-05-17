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
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CartRuleAssertionFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @Then Cart rule with reference :cartRuleReference is enabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws RuntimeException
     */
    public function assertCartRuleEnabled(string $cartRuleReference): void
    {
        /** @var CartRuleForEditing $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));

        Assert::assertTrue(
            $cartRule->getInformation()->isEnabled(),
            sprintf('Cart rule %s is not enabled', $cartRuleReference)
        );
    }

    /**
     * @Then Cart rule with reference :cartRuleReference is disabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     */
    public function assertCartRuleDisabled(string $cartRuleReference): void
    {
        /** @var CartRuleForEditing $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));

        Assert::assertFalse(
            $cartRule->getInformation()->isEnabled(),
            sprintf('Cart rule %s is not disabled', $cartRuleReference)
        );
    }

    /**
     * @Then Cart rule with reference :cartRuleReference does not exist
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws NoExceptionAlthoughExpectedException
     */
    public function assertCartRuleDeleted(string $cartRuleReference): void
    {
        try {
            $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));
            throw new NoExceptionAlthoughExpectedException(sprintf('Cart rule "%s" was found, but it was expected to be deleted', $cartRuleReference));
        } catch (CartRuleNotFoundException $e) {
            $this->getSharedStorage()->clear($cartRuleReference);
        }
    }

    /**
     * @Then I should get cart rule error about :error
     *
     * @param string $error
     *
     * @return void
     */
    public function assertCartRuleError(string $error): void
    {
        $errorMap = [
            'required specific product' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT,
            ],
            'non unique cart rule code' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::NON_UNIQUE_CODE,
            ],
            'missing action' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::MISSING_ACTION,
            ],
            'invalid cart rule restriction' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::INVALID_CART_RULE_RESTRICTION,
            ],
            'non-existing cart rule' => [
                'class' => CartRuleNotFoundException::class,
                'code' => 0,
            ],
        ];

        if (!isset($errorMap[$error])) {
            throw new RuntimeException(sprintf('$error "%s" not set in error map for assertion', $error));
        }

        $this->assertLastErrorIs(
            $errorMap[$error]['class'],
            $errorMap[$error]['code']
        );
    }

    /**
     * @Then cart rule :cartRuleReference should have the following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $tableNode
     */
    public function assertCartRuleProperties(string $cartRuleReference, TableNode $tableNode): void
    {
        /** @var CartRuleForEditing $editableCartRule */
        $editableCartRule = $this->getQueryBus()->handle(
            new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference))
        );

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

        if (isset($data['restricted cart rules'])) {
            $expectedRestrictedCartRuleIds = $this->referencesToIds($data['restricted cart rules']);
            Assert::assertSame(
                $expectedRestrictedCartRuleIds,
                $editableCartRule->getConditions()->getRestrictions()->restrictedCartRuleIds,
                'Unexpected cart rule restrictions'
            );
        }
    }
}
