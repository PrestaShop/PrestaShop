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

use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\CartRule\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractCartRuleFeatureContext extends AbstractDomainFeatureContext
{
    public function createCartRuleWithReference(string $cartRuleReference, array $data): void
    {
        $command = new AddCartRuleCommand(
            $data['name'],
            $this->getCartRuleActionBuilder()->build($this->formatDataForActionBuilder($data))
        );

        if (isset($data['highlight'])) {
            $command->setHighlightInCart(PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']));
        }
        if (isset($data['allow_partial_use'])) {
            $command->setAllowPartialUse(PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']));
        }
        if (isset($data['priority'])) {
            $command->setPriority((int) $data['priority']);
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['valid_from'])) {
            if (empty($data['valid_to'])) {
                throw new RuntimeException('When setting cart rule range "valid_from" and "valid_to" must be provided');
            }
            $command->setValidityDateRange(
                new DateTimeImmutable($data['valid_from']),
                new DateTimeImmutable($data['valid_to']),
            );
        }
        if (isset($data['total_quantity'])) {
            $command->setTotalQuantity((int) $data['total_quantity']);
        }

        if (isset($data['quantity_per_user'])) {
            $command->setQuantityPerUser((int) $data['quantity_per_user']);
        }
        if (!empty($data['minimum_amount'])) {
            $currencyId = $this->getSharedStorage()->get($data['minimum_amount_currency']);
            $command->setMinimumAmount(
                $data['minimum_amount'],
                $currencyId,
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included'])
            );
        }

        $command->setDescription($data['description'] ?? '');
        if (!empty($data['code'])) {
            $command->setCode($data['code']);
        }

        /** @var CartRuleId $cartRuleId */
        $cartRuleId = $this->getCommandBus()->handle($command);
        $this->getSharedStorage()->set($cartRuleReference, $cartRuleId->getValue());

        if (!empty($data['code'])) {
            // set cart rule id by code when it is not empty
            $this->getSharedStorage()->set($data['code'], $cartRuleId->getValue());
        }
    }

    protected function assertCartRuleProperties(CartRuleForEditing $cartRuleForEditing, array $expectedData): void
    {
        $information = $cartRuleForEditing->getInformation();
        $conditions = $cartRuleForEditing->getConditions();
        $actions = $cartRuleForEditing->getActions();

        if (isset($expectedData['name'])) {
            Assert::assertSame($expectedData['name'], $information->getLocalizedNames(), 'Unexpected localized name');
        }
        if (isset($expectedData['description'])) {
            Assert::assertSame($expectedData['description'], $information->getDescription(), 'Unexpected description');
        }
        if (isset($expectedData['highlight'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['highlight']),
                $information->isHighlight(),
                'Unexpected highlight'
            );
        }
        if (isset($expectedData['allow_partial_use'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['allow_partial_use']),
                $information->isPartialUse(),
                'Unexpected partial use'
            );
        }
        if (isset($expectedData['active'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['active']),
                $information->isEnabled(),
                'Unexpected active property'
            );
        }
        if (isset($expectedData['code'])) {
            Assert::assertSame($expectedData['code'], $information->getCode(), 'Unexpected code');
        }
        if (isset($expectedData['customer'])) {
            Assert::assertSame(
                !empty($expectedData['customer']) ? $this->getSharedStorage()->get($expectedData['customer']) : 0,
                $conditions->getCustomerId()->getValue(),
                'Unexpected customer id'
            );
        }
        if (isset($expectedData['priority'])) {
            Assert::assertSame((int) $expectedData['priority'], $information->getPriority(), 'Unexpected priority');
        }
        if (isset($expectedData['valid_from'])) {
            Assert::assertEquals(
                $expectedData['valid_from'],
                $conditions->getDateFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_from'
            );
        }
        if (isset($expectedData['valid_to'])) {
            Assert::assertEquals(
                $expectedData['valid_to'],
                $conditions->getDateTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_to'
            );
        }
        if (isset($expectedData['total_quantity'])) {
            Assert::assertSame((int) $expectedData['total_quantity'], $conditions->getQuantity(), 'Unexpected quantity');
        }
        if (isset($expectedData['quantity_per_user'])) {
            Assert::assertSame((int) $expectedData['quantity_per_user'], $conditions->getQuantityPerUser(), 'Unexpected quantity_per_user');
        }

        if (isset($expectedData['minimum_amount'])) {
            if (empty($expectedData['minimum_amount'])) {
                Assert::assertNull($conditions->getMinimum(), 'unexpected minimum_amount');
            } else {
                $minimum = $conditions->getMinimum();
                Assert::assertTrue(
                    $minimum->getAmount()->equals(new DecimalNumber($expectedData['minimum_amount'])),
                    'Unexpected minimum_amount'
                );

                Assert::assertSame(
                    PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['minimum_amount_tax_included']),
                    $minimum->isAmountTax(),
                    'Unexpected minimum_amount_tax_included'
                );

                Assert::assertSame(
                    $this->getSharedStorage()->get($expectedData['minimum_amount_currency']),
                    $minimum->getCurrencyId(),
                    'Unexpected minimum_amount_currency'
                );

                Assert::assertSame(
                    PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['minimum_amount_shipping_included']),
                    $minimum->isShipping(),
                    'Unexpected minimum_amount_shipping_included'
                );
            }
        }

        if (isset($expectedData['discount_application_type'])) {
            $expectedDiscountApplicationType = $expectedData['discount_application_type'];
            Assert::assertSame(
                $expectedDiscountApplicationType,
                $actions->getDiscountApplicationType(),
                'Unexpected discount_application_type'
            );

            Assert::assertSame(
                !empty($expectedData['discount_product']) ? $this->getSharedStorage()->get($expectedData['discount_product']) : null,
                $actions->getReduction()->getProductId(),
                'Unexpected discount_product'
            );
        }

        if (isset($expectedData['discount_percentage'])) {
            Assert::assertTrue(
                $actions->getReduction()->getPercent()->equals(new DecimalNumber($expectedData['discount_percentage'])),
                'Unexpected discount_percentage'
            );
        }

        if (isset($expectedData['discount_amount'])) {
            Assert::assertTrue(
                $actions->getReduction()->getAmount()->equals(new DecimalNumber($expectedData['discount_amount'])),
                'Unexpected discount_amount'
            );
        }

        if (isset($expectedData['discount_currency'])) {
            if ($expectedData['discount_currency'] === '') {
                Assert::assertNull(
                    $actions->getReduction()->getCurrencyId(),
                    'Unexpected discount_currency'
                );
            } else {
                Assert::assertSame(
                    $this->getSharedStorage()->get($expectedData['discount_currency']),
                    $actions->getReduction()->getCurrencyId(),
                    'Unexpected discount_currency'
                );
            }
        }

        if (isset($expectedData['discount_includes_tax'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['discount_includes_tax']),
                $actions->getReduction()->isTax(),
                'Unexpected discount_includes_tax'
            );
        }

        if (isset($expectedData['apply_to_discounted_products'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['apply_to_discounted_products']),
                $actions->getReduction()->applyToDiscountedProducts(),
                'Unexpected apply_to_discounted_products'
            );
        }

        if (isset($expectedData['free_shipping'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['free_shipping']),
                $actions->isFreeShipping(),
                'Unexpected free_shipping'
            );
        }

        if (isset($expectedData['gift_product'])) {
            Assert::assertSame(
                $this->getSharedStorage()->get($expectedData['gift_product']),
                $actions->getGiftProductId(),
                'Unexpected gift_product'
            );
        }

        if (isset($expectedData['gift_combination'])) {
            Assert::assertSame(
                !empty($expectedData['gift_combination']) ? $this->getSharedStorage()->get($expectedData['gift_combination']) : null,
                $actions->getGiftCombinationId(),
                'Unexpected gift_combination'
            );
        }

        if (isset($expectedData['restricted cart rules'])) {
            $expectedRestrictedCartRuleIds = $this->referencesToIds($expectedData['restricted cart rules']);
            Assert::assertSame(
                $expectedRestrictedCartRuleIds,
                $conditions->getRestrictions()->getRestrictedCartRuleIds(),
                'Unexpected cart rule restrictions'
            );
        }

        if (isset($expectedData['restricted carriers'])) {
            Assert::assertSame(
                $this->referencesToIds($expectedData['restricted carriers']),
                $conditions->getRestrictions()->getRestrictedCarrierIds(),
                'Unexpected restricted carriers'
            );
        }
    }

    protected function getCartRuleActionBuilder(): CartRuleActionBuilder
    {
        return new CartRuleActionBuilder();
    }

    /**
     * This method reformats the data from the behat feature files into an array structure understandable by the CartRuleActionBuilder (based on the form structure).
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function formatDataForActionBuilder(array $data): array
    {
        $formattedData = [];

        if (isset($data['free_shipping'])) {
            $formattedData['free_shipping'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']);
        }

        if (isset($data['discount_percentage'])) {
            $formattedData['discount']['reduction']['value'] = $data['discount_percentage'];
            $formattedData['discount']['reduction']['type'] = Reduction::TYPE_PERCENTAGE;
            if (isset($data['apply_to_discounted_products'])) {
                $formattedData['discount']['apply_to_discounted_products'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['apply_to_discounted_products']);
            }
        }

        if (isset($data['discount_amount'])) {
            $formattedData['discount']['reduction'] = [
                'value' => $data['discount_amount'],
                'type' => Reduction::TYPE_AMOUNT,
                'currency' => $this->getSharedStorage()->get($data['discount_currency']),
                'include_tax' => PrimitiveUtils::castStringBooleanIntoBoolean($data['discount_includes_tax']),
            ];
        }

        if (isset($data['discount_application_type'])) {
            $formattedData['discount']['discount_application'] = $data['discount_application_type'];
        }

        if (isset($data['discount_product'])) {
            $formattedData['discount']['specific_product'][0]['id'] = (int) $this->getSharedStorage()->get($data['discount_product']);
        }

        if (isset($data['gift_product'])) {
            $formattedData['gift_product'][0]['product_id'] = (int) $this->getSharedStorage()->get($data['gift_product']);
            if (isset($data['gift_combination'])) {
                $formattedData['gift_product'][0]['combination_id'] = $this->getSharedStorage()->get($data['gift_combination']);
            }
        }

        return $formattedData;
    }

    protected function getCartRuleForEditing(string $cartRuleReference): CartRuleForEditing
    {
        /** @var CartRuleForEditing $cartRuleForEditing */
        $cartRuleForEditing = $this->getQueryBus()->handle(
            new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference))
        );

        return $cartRuleForEditing;
    }
}
