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

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkDeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\DeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Integration\Behaviour\Features\Transform\StringToBoolTransformContext;

class CartRuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create cart rule :cartRuleReference with following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $node
     *
     * @throws CartRuleConstraintException
     * @throws DomainConstraintException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createCartRuleWithReference(string $cartRuleReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);

        try {
            $cartRuleAction = $this->createCartRuleAction(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']),
                $data['reduction_percentage'] ?? null,
                isset($data['reduction_apply_to_discounted_products']) ? PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_apply_to_discounted_products']) : null,
                $data['reduction_amount'] ?? null,
                isset($data['reduction_currency']) ? $this->getSharedStorage()->get($data['reduction_currency']) : null,
                $data['reduction_tax'] ?? null,
                $data['gift_product_id'] ?? null,
                $data['gift_product_attribute_id'] ?? null
            );

            $command = new AddCartRuleCommand(
                $data['name'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']),
                (int) $data['priority'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['is_active']),
                new DateTime($data['valid_from']),
                new DateTime($data['valid_to']),
                $data['total_quantity'],
                $data['quantity_per_user'],
                $cartRuleAction
            );

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

            if (isset($data['discount_application_type'])) {
                $command->setDiscountApplication(
                    $data['discount_application_type'],
                    // if specific product type is provided and product is not, then command should throw exception
                    isset($data['discount_product']) ? $this->getSharedStorage()->get($data['discount_product']) : null
                );
            }

            /** @var CartRuleId $cartRuleId */
            $cartRuleId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($cartRuleReference, $cartRuleId->getValue());

            if (!empty($data['code'])) {
                // set cart rule id by code when it is not empty
                $this->getSharedStorage()->set($data['code'], $cartRuleId->getValue());
            }
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete Cart rule with reference :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     */
    public function deleteCartRule(string $cartRuleReference): void
    {
        $this->getCommandBus()->handle(
            new DeleteCartRuleCommand($this->getSharedStorage()->get($cartRuleReference))
        );
    }

    /**
     * @When /^I (enable|disable) cart rule with reference "(.+)"$/
     *
     * @param bool $enable
     * @param string $cartRuleReference
     *
     * @see StringToBoolTransformContext::transformTruthyStringToBoolean for $enable string to bool transformation
     */
    public function toggleCartRuleStatus(bool $enable, string $cartRuleReference): void
    {
        $this->getCommandBus()->handle(
            new ToggleCartRuleStatusCommand($this->getSharedStorage()->get($cartRuleReference), $enable)
        );
    }

    /**
     * @When /^I bulk (enable|disable) cart rules "(.+)"$/
     *
     * @param string $cartRuleReferences
     */
    public function bulkEnableCartRules(bool $enable, string $cartRuleReferences): void
    {
        $this->getCommandBus()->handle(
            new BulkToggleCartRuleStatusCommand($this->referencesToIds($cartRuleReferences), $enable)
        );
    }

    /**
     * @When I bulk delete cart rules :cartRuleReferences
     *
     * @param string $cartRuleReferences
     *
     * @throws CartRuleConstraintException
     */
    public function bulkDeleteCartRules(string $cartRuleReferences): void
    {
        $this->getCommandBus()->handle(new BulkDeleteCartRuleCommand($this->referencesToIds($cartRuleReferences)));
    }

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
     * @Then I should get cart rule error about :errorName
     *
     * @param string $errorName
     *
     * @return void
     */
    public function assertCartRuleConstraintError(string $errorName): void
    {
        $errorMap = [
            'missing action' => CartRuleConstraintException::MISSING_ACTION,
            'required specific product' => CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT,
            'non unique cart rule code' => CartRuleConstraintException::NON_UNIQUE_CODE,
        ];

        $this->assertLastErrorIs(
            CartRuleConstraintException::class,
            $errorMap[$errorName]
        );
    }

    /**
     * Create a cart rule action that can be used for cart rule creation.
     *
     * @param bool $isFreeShipping
     * @param string|null $percentage
     * @param bool|null $applyToDiscountedProducts
     * @param string|null $amount
     * @param int|null $amountCurrencyId
     * @param bool|null $amountTaxIncluded
     * @param int|null $giftProductId
     * @param int|null $giftProductCombinationId
     *
     * @return CartRuleActionInterface
     */
    private function createCartRuleAction(
        bool $isFreeShipping,
        ?string $percentage = null,
        ?bool $applyToDiscountedProducts = null,
        ?string $amount = null,
        ?int $amountCurrencyId = null,
        ?bool $amountTaxIncluded = null,
        ?int $giftProductId = null,
        ?int $giftProductCombinationId = null
    ): CartRuleActionInterface {
        $builder = new CartRuleActionBuilder();

        $builder->setFreeShipping($isFreeShipping);

        if (null !== $percentage) {
            $builder->setPercentageDiscount(
                $percentage,
                $applyToDiscountedProducts
            );
        }

        if (null !== $amount) {
            $builder->setAmountDiscount(
                $amount,
                $amountCurrencyId,
                $amountTaxIncluded
            );
        }

        if (null !== $giftProductId) {
            $builder->setGiftProduct($giftProductId, $giftProductCombinationId);
        }

        return $builder->build();
    }
}
