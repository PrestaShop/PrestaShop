<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain\Discount;

use Behat\Gherkin\Node\TableNode;
use Cart;
use CartRule;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkDeleteDiscountsCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkUpdateDiscountsStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DeleteDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DuplicateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class DiscountFeatureContext extends AbstractDomainFeatureContext
{
    private const DISCOUNT_TYPE_PREFIX = 'discount_type_';

    /**
     * @Then I should get error that discount field :field is invalid
     */
    public function assertDiscountInvalidField(string $field): void
    {
        $errorCode = match ($field) {
            'name' => DiscountConstraintException::INVALID_NAME,
            'gift_product' => DiscountConstraintException::INVALID_GIFT_PRODUCT,
            'description' => DiscountConstraintException::INVALID_DESCRIPTION,
            default => null,
        };

        $this->assertLastErrorIs(DiscountConstraintException::class, $errorCode);
    }

    /**
     * @Then I should get an error that the discount code is already used
     */
    public function assertDiscountCodeAlreadyUsed(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::NON_UNIQUE_CODE);
    }

    /**
     * @Then I should get an error that the discount is invalid
     */
    public function assertDiscountIsInvalid(): void
    {
        $this->assertLastErrorIs(CartRuleValidityException::class);
    }

    /**
     * @Then I should get an error that start date cannot be after expiry date
     */
    public function assertStartDateCannotBeAfterExpiryDate(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::DATE_FROM_GREATER_THAN_DATE_TO);
    }

    /**
     * @Then I should get an error that the discount target is missing
     */
    public function assertDiscountNoTarget(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_MISSING_TARGET);
    }

    /**
     * @Then I should get an error that the discount targets are incompatible
     */
    public function assertDiscountIncompatibleTargets(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_TARGETS);
    }

    /**
     * @Then I should get an error that the discount reduction is missing
     */
    public function assertDiscountMissingReduction(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::INVALID_MISSING_REDUCTION);
    }

    /**
     * @Then I should get an error that the discount reductions are incompatible
     */
    public function assertDiscountIncompatibleReductions(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_REDUCTIONS);
    }

    /**
     * @Then I should get an error that discount cannot be assigned to guest customers
     */
    public function assertDiscountCannotBeAssignedToGuestCustomers(): void
    {
        $this->assertLastErrorIs(DiscountConstraintException::class, DiscountConstraintException::INVALID_GUEST_CUSTOMER);
    }

    /**
     * @Then discount :discountReference should have the following properties:
     *
     * @param string $discountReference
     * @param TableNode $tableNode
     */
    public function assertDiscount(string $discountReference, TableNode $tableNode): void
    {
        try {
            // If discount already exists we assert all its expected properties
            $discountForEditing = $this->getDiscountForEditing($discountReference);
            $this->assertDiscountProperties(
                $discountForEditing,
                $this->filterOutProductConditions($tableNode),
            );

            // Now check product conditions if present
            $productConditions = $this->parseProductConditions($tableNode);
            if (null !== $productConditions) {
                Assert::assertEquals($productConditions, $discountForEditing->getProductConditions());
            }
        } catch (DiscountException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then discount :discountReference shouldn't have :codeNotExpected as code
     *
     * @param string $discountReference
     * @param string $codeNotExpected
     */
    public function assertNotDiscountCode(string $discountReference, string $codeNotExpected): void
    {
        try {
            $discount = $this->getDiscountForEditing($discountReference);
            Assert::assertNotSame(
                $codeNotExpected,
                $discount->getCode(),
                sprintf('Discount "%s" has unexpected code "%s"', $discountReference, $codeNotExpected)
            );
        } catch (DiscountNotFoundException $e) {
            $this->getSharedStorage()->clear($discountReference);
        }
    }

    /**
     * @When I create a :discountType discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     *
     * @throws DiscountConstraintException
     * @throws Exception
     */
    public function createDiscount(string $discountReference, string $discountType, TableNode $node): void
    {
        $data = $this->filterOutProductConditions($node);
        $command = new AddDiscountCommand($discountType, $data['name']);

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
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
            $validFrom = new DateTimeImmutable($data['valid_from']);

            if (isset($data['period_never_expires']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['period_never_expires'])) {
                $validTo = null;
            } elseif (!empty($data['valid_to'])) {
                $validTo = new DateTimeImmutable($data['valid_to']);
            } else {
                throw new RuntimeException('When setting cart rule range "valid_from" and "valid_to" must be provided, or "period_never_expires" must be set');
            }

            try {
                $command->setValidityDateRange($validFrom, $validTo);
            } catch (DiscountConstraintException $e) {
                $this->setLastException($e);
            }
        }
        if (isset($data['total_quantity'])) {
            if ($data['total_quantity'] === 'null') {
                $command->setTotalQuantity(null);
            } else {
                $command->setTotalQuantity((int) $data['total_quantity']);
            }
        }
        if (isset($data['quantity_per_user'])) {
            if ($data['quantity_per_user'] === 'null') {
                $command->setQuantityPerUser(null);
            } else {
                $command->setQuantityPerUser((int) $data['quantity_per_user']);
            }
        }

        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (!empty($data['code'])) {
            $command->setCode($data['code']);
        }

        if (isset($data['customer']) && !empty($data['customer'])) {
            $command->setCustomerId($this->getSharedStorage()->get($data['customer']));
        }

        if (in_array($command->getDiscountType()->getValue(), [
            DiscountType::CART_LEVEL,
            DiscountType::PRODUCT_LEVEL,
            DiscountType::ORDER_LEVEL,
        ])) {
            if (!empty($data['reduction_percent'])) {
                $command->setReductionPercent(new DecimalNumber($data['reduction_percent']));
            }

            if (!empty($data['reduction_amount'])) {
                try {
                    $command->setReductionAmount(
                        new DecimalNumber($data['reduction_amount']),
                        $this->getSharedStorage()->get($data['reduction_currency']),
                        PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_tax_included']),
                    );
                } catch (DiscountConstraintException $e) {
                    $this->setLastException($e);
                }
            }
        }

        if ($command->getDiscountType()->getValue() === DiscountType::PRODUCT_LEVEL) {
            if (!empty($data['cheapest_product'])) {
                $command->setCheapestProduct(PrimitiveUtils::castStringBooleanIntoBoolean($data['cheapest_product']));
            }
            if (isset($data['reduction_product'])) {
                if (empty($data['reduction_product'])) {
                    $command->setReductionProductId(null);
                } else {
                    $command->setReductionProductId($this->referenceToId($data['reduction_product']));
                }
            }
        }

        if ($command->getDiscountType()->getValue() === DiscountType::FREE_GIFT) {
            if (!empty($data['gift_product'])) {
                $command->setGiftProductId($this->referenceToId($data['gift_product']));
            }

            if (!empty($data['gift_combination'])) {
                $command->setGiftCombinationId($this->referenceToId($data['gift_combination']));
            }
        }

        if (isset($data['customer_groups'])) {
            $command->setCustomerGroupIds($this->referencesToIds($data['customer_groups']));
        }

        if (isset($data['compatible_types'])) {
            $command->setCompatibleDiscountTypeIds($this->getDiscountTypeIds($data['compatible_types']));
        }

        $productConditions = $this->parseProductConditions($node);
        if (null !== $productConditions) {
            $command->setProductConditions($productConditions);
        }

        if (isset(
            $data['minimum_amount'],
            $data['minimum_amount_currency'],
            $data['minimum_amount_tax_included'],
            $data['minimum_amount_shipping_included'])
        ) {
            $command->setMinimumAmount(
                new DecimalNumber($data['minimum_amount']),
                $this->referenceToId($data['minimum_amount_currency']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']),
            );
        }

        if (isset($data['minimum_product_quantity'])) {
            $command->setMinimumProductQuantity((int) $data['minimum_product_quantity']);
        }

        if (isset($data['carriers'])) {
            $command->setCarrierIds($this->referencesToIds($data['carriers']));
        }

        if (isset($data['countries'])) {
            $command->setCountryIds($this->referencesToIds($data['countries']));
        }

        try {
            /** @var DiscountId $discountId */
            $discountId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($discountReference, $discountId->getValue());
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a :discountType discount :discountReference with a very large description
     */
    public function createDiscountWithVeryLargeDescription(string $discountReference, string $discountType): void
    {
        $command = new AddDiscountCommand($discountType, ['en-US' => 'Test Discount']);
        $command->setDescription(str_repeat('A', DiscountSettings::MAX_DESCRIPTION_LENGTH + 1));

        try {
            /** @var DiscountId $discountId */
            $discountId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($discountReference, $discountId->getValue());
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update discount :discountReference with the following properties:
     */
    public function editDiscount(string $discountReference, TableNode $node): void
    {
        $data = $this->filterOutProductConditions($node);
        $discountId = $this->getSharedStorage()->get($discountReference);
        $command = new UpdateDiscountCommand($discountId);

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
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
        if (isset($data['period_never_expires']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['period_never_expires'])) {
            if (isset($data['valid_from'])) {
                $validFrom = new DateTimeImmutable($data['valid_from']);
            } else {
                $validFrom = new DateTimeImmutable();
            }

            try {
                $command->setValidityDateRange($validFrom, null);
            } catch (DiscountConstraintException $e) {
                $this->setLastException($e);
            }
        } elseif (isset($data['valid_from'], $data['valid_to'])) {
            try {
                $command->setValidityDateRange(
                    new DateTimeImmutable($data['valid_from']),
                    new DateTimeImmutable($data['valid_to']),
                );
            } catch (DiscountConstraintException $e) {
                $this->setLastException($e);
            }
        } elseif (isset($data['valid_from'])) {
            $command->setValidFrom(new DateTimeImmutable($data['valid_from']));
        } elseif (isset($data['valid_to'])) {
            $command->setValidTo(new DateTimeImmutable($data['valid_to']));
        }
        if (isset($data['total_quantity'])) {
            if ($data['total_quantity'] === 'null') {
                $command->setTotalQuantity(null);
            } else {
                $command->setTotalQuantity((int) $data['total_quantity']);
            }
        }
        if (isset($data['quantity_per_user'])) {
            if ($data['quantity_per_user'] === 'null') {
                $command->setQuantityPerUser(null);
            } else {
                $command->setQuantityPerUser((int) $data['quantity_per_user']);
            }
        }

        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (!empty($data['code'])) {
            $command->setCode($data['code']);
        }

        if (isset($data['customer'])) {
            if (!empty($data['customer'])) {
                $command->setCustomerId($this->getSharedStorage()->get($data['customer']));
            } else {
                // Empty string means remove customer restriction
                $command->setCustomerId(0);
            }
        }

        if (!empty($data['reduction_percent'])) {
            $command->setReductionPercent(new DecimalNumber($data['reduction_percent']));
        }

        if (!empty($data['reduction_amount'])) {
            try {
                $command->setReductionAmount(
                    new DecimalNumber($data['reduction_amount']),
                    $this->getSharedStorage()->get($data['reduction_currency']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_tax_included']),
                );
            } catch (DiscountConstraintException $e) {
                $this->setLastException($e);
            }
        }

        if (!empty($data['cheapest_product'])) {
            $command->setCheapestProduct(PrimitiveUtils::castStringBooleanIntoBoolean($data['cheapest_product']));
        }

        if (isset($data['reduction_product'])) {
            if (empty($data['reduction_product'])) {
                $command->setReductionProductId(null);
            } else {
                $command->setReductionProductId($this->referenceToId($data['reduction_product']));
            }
        }

        if (!empty($data['gift_product'])) {
            $command->setGiftProductId($this->referenceToId($data['gift_product']));
        }

        if (!empty($data['gift_combination'])) {
            $command->setGiftCombinationId($this->referenceToId($data['gift_combination']));
        }

        if (isset($data['customer_groups'])) {
            $command->setCustomerGroupIds($this->referencesToIds($data['customer_groups']));
        }
        if (isset($data['compatible_types'])) {
            $command->setCompatibleDiscountTypeIds($this->getDiscountTypeIds($data['compatible_types']));
        }
        $productConditions = $this->parseProductConditions($node);
        if (null !== $productConditions) {
            $command->setProductConditions($productConditions);
        }
        if (isset($data['minimum_amount'])) {
            if (isset($data['minimum_amount_currency'],
                $data['minimum_amount_tax_included'],
                $data['minimum_amount_shipping_included'])) {
                $command->setMinimumAmount(
                    new DecimalNumber($data['minimum_amount']),
                    $this->referenceToId($data['minimum_amount_currency']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']),
                );
            } elseif (empty($data['minimum_amount'])) {
                $command->setMinimumAmount(null);
            }
        }
        if (isset($data['minimum_product_quantity'])) {
            $command->setMinimumProductQuantity((int) $data['minimum_product_quantity']);
        }

        if (isset($data['carriers'])) {
            $command->setCarrierIds($this->referencesToIds($data['carriers']));
        }

        if (isset($data['countries'])) {
            $command->setCountryIds($this->referencesToIds($data['countries']));
        }

        try {
            /* @var DiscountId $discountId */
            $this->getCommandBus()->handle($command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I delete discount :discountReference:
     */
    public function deleteDiscount(string $discountReference): void
    {
        $discountId = $this->getSharedStorage()->get($discountReference);
        $command = new DeleteDiscountCommand($discountId);

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then discount :discountReference should not exist anymore:
     */
    public function assertDiscountIsNotFound(string $discountReference): void
    {
        try {
            $this->getDiscountForEditing($discountReference);
            throw new NoExceptionAlthoughExpectedException(sprintf('Discount "%s" was found, but it was expected to be deleted', $discountReference));
        } catch (DiscountNotFoundException $e) {
            $this->getSharedStorage()->clear($discountReference);
        }
    }

    protected function assertDiscountProperties(DiscountForEditing $discountForEditing, array $expectedData): void
    {
        if (isset($expectedData['description'])) {
            Assert::assertSame($expectedData['description'], $discountForEditing->getDescription(), 'Unexpected description');
        }
        if (isset($expectedData['highlight'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['highlight']),
                $discountForEditing->isHighlightInCart(),
                'Unexpected highlight'
            );
        }
        if (isset($expectedData['allow_partial_use'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['allow_partial_use']),
                $discountForEditing->isAllowPartialUse(),
                'Unexpected partial use'
            );
        }
        if (isset($expectedData['active'])) {
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['active']),
                $discountForEditing->isActive(),
                'Unexpected active property'
            );
        }
        if (isset($expectedData['code'])) {
            Assert::assertSame($expectedData['code'], $discountForEditing->getCode(), 'Unexpected code');
        }
        if (isset($expectedData['customer'])) {
            $expectedCustomerId = !empty($expectedData['customer']) ? (int) $this->getSharedStorage()->get($expectedData['customer']) : 0;
            $actualCustomerId = $discountForEditing->getCustomerId() ?? 0;
            Assert::assertSame(
                $expectedCustomerId,
                $actualCustomerId,
                'Unexpected customer id'
            );
        }

        if (isset($expectedData['customer_groups'])) {
            $expectedGroupIds = $this->referencesToIds($expectedData['customer_groups']);
            $actualGroupIds = $discountForEditing->getCustomerGroupIds();
            sort($expectedGroupIds);
            sort($actualGroupIds);
            Assert::assertSame(
                $expectedGroupIds,
                $actualGroupIds,
                'Unexpected customer group ids'
            );
        }
        if (isset($expectedData['priority'])) {
            Assert::assertSame((int) $expectedData['priority'], $discountForEditing->getPriority(), 'Unexpected priority');
        }
        if (isset($expectedData['valid_from'])) {
            Assert::assertEquals(
                $expectedData['valid_from'],
                $discountForEditing->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_from'
            );
        }
        if (isset($expectedData['valid_to'])) {
            Assert::assertEquals(
                $expectedData['valid_to'],
                $discountForEditing->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected valid_to'
            );
        }
        if (isset($expectedData['total_quantity'])) {
            if ($expectedData['total_quantity'] === 'null') {
                Assert::assertNull($discountForEditing->getTotalQuantity(), 'Unexpected total_quantity, expected null');
            } else {
                Assert::assertSame((int) $expectedData['total_quantity'], $discountForEditing->getTotalQuantity(), 'Unexpected total_quantity');
            }
        }
        if (isset($expectedData['quantity_used_in_orders'])) {
            Assert::assertSame((int) $expectedData['quantity_used_in_orders'], $discountForEditing->getQuantityUsedInOrders(), 'Unexpected quantity_used_in_orders');
        }
        if (isset($expectedData['remaining_quantity'])) {
            if ($expectedData['remaining_quantity'] === 'null') {
                Assert::assertNull($discountForEditing->getRemainingQuantity(), 'Unexpected remaining_quantity, expected null');
            } else {
                Assert::assertSame((int) $expectedData['remaining_quantity'], $discountForEditing->getRemainingQuantity(), 'Unexpected remaining_quantity');
            }
        }
        if (isset($expectedData['quantity_per_user'])) {
            if ($expectedData['quantity_per_user'] === 'null') {
                Assert::assertNull($discountForEditing->getQuantityPerUser(), 'Unexpected quantity_per_user, expected null');
            } else {
                Assert::assertSame((int) $expectedData['quantity_per_user'], $discountForEditing->getQuantityPerUser(), 'Unexpected quantity_per_user');
            }
        }

        if (isset($expectedData['reduction_percent'])) {
            if (empty($expectedData['reduction_percent'])) {
                Assert::assertNull($discountForEditing->getReductionPercent(), 'Expected percent discount to be null');
            } else {
                Assert::assertSame((float) $expectedData['reduction_percent'], (float) (string) $discountForEditing->getReductionPercent(), 'Unexpected percent discount');
            }
        }

        if (isset($expectedData['reduction_amount'])) {
            if (empty($expectedData['reduction_amount'])) {
                Assert::assertNull($discountForEditing->getReductionAmount(), 'Expected amount discount to be null');
            } else {
                Assert::assertSame((float) $expectedData['reduction_amount'], (float) (string) $discountForEditing->getReductionAmount()->getAmount(), 'Unexpected amount discount');
            }
        }
        if (isset($expectedData['reduction_currency'])) {
            if (empty($expectedData['reduction_currency'])) {
                Assert::assertNull($discountForEditing->getReductionAmount(), 'Unexpected reduction amount currency');
            } else {
                Assert::assertSame($this->getSharedStorage()->get($expectedData['reduction_currency']), $discountForEditing->getReductionAmount()->getCurrencyId(), 'Unexpected reduction currency');
            }
        }
        if (isset($expectedData['reduction_tax_included'])) {
            if (empty($expectedData['reduction_tax_included'])) {
                Assert::assertNull($discountForEditing->getReductionAmount(), 'Unexpected reduction amount tax included');
            } else {
                Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['reduction_tax_included']), $discountForEditing->getReductionAmount()->isTaxIncluded(), 'Unexpected tax included');
            }
        }
        if (isset($expectedData['type'])) {
            Assert::assertSame($expectedData['type'], $discountForEditing->getType()->getValue(), 'Unexpected type');
        }
        if (isset($expectedData['cheapest_product'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['cheapest_product']), $discountForEditing->getCheapestProduct(), 'Unexpected cheapest_product');
        }
        if (isset($expectedData['reduction_product'])) {
            if (empty($expectedData['reduction_product'])) {
                Assert::assertNull($discountForEditing->getReductionProductId(), 'Unexpected reduction product');
            } else {
                Assert::assertEquals($this->referenceToId($expectedData['reduction_product']), $discountForEditing->getReductionProductId(), 'Unexpected reduction product');
            }
        }
        if (isset($expectedData['name'])) {
            Assert::assertSame($expectedData['name'], $discountForEditing->getLocalizedNames());
        }
        if (isset($expectedData['minimum_product_quantity'])) {
            Assert::assertEquals($expectedData['minimum_product_quantity'], $discountForEditing->getMinimumProductQuantity(), 'Unexpected minimum quantity');
        }
        if (isset($expectedData['minimum_amount'])) {
            if (empty($expectedData['minimum_amount'])) {
                Assert::assertNull($discountForEditing->getMinimumAmount(), 'Unexpected minimum amount');
            } else {
                Assert::assertSame((float) $expectedData['minimum_amount'], (float) (string) $discountForEditing->getMinimumAmount()->getAmount(), 'Unexpected minimum amount');
            }
        }
        if (isset($expectedData['minimum_amount_currency'])) {
            if (empty($expectedData['minimum_amount_currency'])) {
                Assert::assertNull($discountForEditing->getMinimumAmount(), 'Unexpected minimum amount currency');
            } else {
                Assert::assertSame($this->getSharedStorage()->get($expectedData['minimum_amount_currency']), $discountForEditing->getMinimumAmount()->getCurrencyId(), 'Unexpected minimum amount currency');
            }
        }
        if (isset($expectedData['minimum_amount_tax_included'])) {
            if (empty($expectedData['minimum_amount_tax_included'])) {
                Assert::assertNull($discountForEditing->getMinimumAmount(), 'Unexpected minimum amount tax included');
            } else {
                Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['minimum_amount_tax_included']), $discountForEditing->getMinimumAmount()->isTaxIncluded(), 'Unexpected minimum amount tax included');
            }
        }
        if (isset($expectedData['minimum_amount_shipping_included'])) {
            if (empty($expectedData['minimum_amount_shipping_included'])) {
                Assert::assertNull($discountForEditing->getMinimumAmount(), 'Unexpected minimum amount shipping included');
            } else {
                Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['minimum_amount_shipping_included']), $discountForEditing->getMinimumAmount()->isShippingIncluded(), 'Unexpected minimum amount shipping included');
            }
        }
        if (isset($expectedData['carriers'])) {
            Assert::assertSame($this->referencesToIds($expectedData['carriers']), $discountForEditing->getCarrierIds(), 'Unexpected carriers');
        }
        if (isset($expectedData['countries'])) {
            Assert::assertSame($this->referencesToIds($expectedData['countries']), $discountForEditing->getCountryIds(), 'Unexpected countries');
        }
        if (isset($expectedData['period_never_expires'])) {
            $neverExpires = $discountForEditing->getValidTo() === null;
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['period_never_expires']),
                $neverExpires,
                'Unexpected period_never_expires value'
            );
        }
        if (isset($expectedData['compatible_types'])) {
            Assert::assertEquals($this->getDiscountTypeIds($expectedData['compatible_types']), $discountForEditing->getCompatibleDiscountTypeIds());
        }
    }

    /**
     * @Then discount :discountReference should have no expiration date
     */
    public function assertDiscountHasNoExpirationDate(string $discountReference): void
    {
        $discountForEditing = $this->getDiscountForEditing($discountReference);
        Assert::assertNull(
            $discountForEditing->getValidTo(),
            'Discount should have no expiration date (period never expires)'
        );
    }

    protected function getDiscountForEditing(string $discountReference): DiscountForEditing
    {
        /** @var DiscountForEditing $discountForEditing */
        $discountForEditing = $this->getQueryBus()->handle(
            new GetDiscountForEditing($this->getSharedStorage()->get($discountReference))
        );

        return $discountForEditing;
    }

    /**
     * @Then cart :cartReference should have :count cart rules applied
     *
     * @param string $cartReference
     * @param int $count
     */
    public function assertCartHasCartRulesCount(string $cartReference, int $count): void
    {
        $cartId = $this->getSharedStorage()->get($cartReference);
        $cart = new Cart($cartId);
        $cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);

        Assert::assertCount(
            $count,
            $cartRules,
            sprintf('Expected %d cart rules but found %d', $count, count($cartRules))
        );
    }

    /**
     * @Given /^discount "(.*)" is (enabled|disabled)$/
     *
     * Status type "enabled|disabled" should be converted by transform context. @see StringToBoolTransformContext
     *
     * @param string $discountReference
     * @param bool $expectedStatus
     */
    public function assertDiscountStatus(string $discountReference, bool $expectedStatus): void
    {
        $discount = $this->getDiscountForEditing($discountReference);
        Assert::assertSame($expectedStatus, $discount->isActive());
    }

    /**
     * @When /^I bulk (enable|disable) discounts "(.*)"$/
     *
     * @param bool $enable
     * @param string $discountReferences
     */
    public function bulkUpdateDiscountsStatus(bool $enable, string $discountReferences)
    {
        $this->getCommandBus()->handle(
            new BulkUpdateDiscountsStatusCommand($this->referencesToIds($discountReferences), $enable)
        );
    }

    /**
     * @When /^I bulk delete discounts "(.*)"$/
     *
     * @param string $discountReferences
     */
    public function bulkDeleteDiscounts(string $discountReferences)
    {
        $this->getCommandBus()->handle(
            new BulkDeleteDiscountsCommand($this->referencesToIds($discountReferences))
        );
    }

    /**
     * @Then /^discount "(.*)" (should|should not) exist$/
     *
     * @param string $discountReference
     * @param bool $shouldExist
     */
    public function assertDiscountExistence(string $discountReference, bool $shouldExist)
    {
        try {
            $this->getDiscountForEditing($discountReference);
        } catch (DiscountNotFoundException) {
            Assert::assertFalse($shouldExist, sprintf('Discount "%s" was not found, but it was expected to exist', $discountReference));

            return;
        }

        Assert::assertTrue($shouldExist, sprintf('Discount "%s" was found, but it was expected to be deleted', $discountReference));
    }

    /**
     * @When I duplicate the discount :discountReference as :copyDiscountReference
     */
    public function duplicateDiscount(string $discountReference, string $copyDiscountReference): void
    {
        try {
            /** @var DiscountId $copyDiscountId */
            $copyDiscountId = $this->getCommandBus()->handle(new DuplicateDiscountCommand(
                $this->getSharedStorage()->get($discountReference)
            ));
            $this->getSharedStorage()->set($copyDiscountReference, $copyDiscountId->getValue());
        } catch (DiscountException $e) {
            $this->setLastException($e);
        }
    }

    private function getDiscountTypeIds(string $discountTypes): array
    {
        if (empty(trim($discountTypes))) {
            return [];
        }

        $discountNamesList = explode(',', $discountTypes);
        $discountTypeIds = [];
        foreach ($discountNamesList as $discountName) {
            $discountTypeIds[] = $this->getDiscountTypeId(trim($discountName));
        }
        sort($discountTypeIds);

        return $discountTypeIds;
    }

    private function getDiscountTypeId(string $discountType): int
    {
        if (!$this->getSharedStorage()->exists(self::DISCOUNT_TYPE_PREFIX . $discountType)) {
            /** @var DiscountTypeRepository $repository */
            $repository = $this->getContainer()->get(DiscountTypeRepository::class);
            $activeTypes = $repository->getAllActiveTypes($this->getDefaultLangId());

            // Cache all existing discount types in shared storage for future references
            foreach ($activeTypes as $activeType) {
                $this->getSharedStorage()->set(self::DISCOUNT_TYPE_PREFIX . $activeType['discount_type'], $activeType['id_cart_rule_type']);
            }
        }

        return (int) $this->getSharedStorage()->get(self::DISCOUNT_TYPE_PREFIX . $discountType);
    }

    private function parseProductConditions(TableNode $node): ?array
    {
        $quantity = 0;
        $productRules = [];
        $testingProductRules = false;
        foreach ($node->getRows() as $tableRow) {
            $key = $tableRow[0];
            if (!str_starts_with($key, 'productCondition')) {
                continue;
            }

            $testingProductRules = true;
            if ($key === 'productConditionQuantity') {
                $quantity = (int) $tableRow[1];
                continue;
            }

            // Get type of condition: productCondition[conditionType]
            preg_match('/productCondition\[(.+)\]/', $key, $matches);
            if (empty($matches)) {
                continue;
            }

            $conditionType = ProductRuleType::from($matches[1]);
            $productRules[] = new ProductRule(
                $conditionType,
                $this->referencesToIds($tableRow[1]),
            );
        }

        if (!$testingProductRules) {
            return null;
        }

        // No rules defined
        if ($quantity <= 0 && empty($productRules)) {
            return [];
        }

        if ($quantity <= 0) {
            throw new RuntimeException('You must define the productConditionQuantity');
        }
        if (empty($productRules)) {
            throw new RuntimeException('No product conditions defined');
        }
        // This matches the current business rule for the new form, a discount can have multiple criteria that are more and more
        // restrictive, each different rule adds more restriction and the quantity is the same To implement this behaviour we use
        // the new ProductRuleGroupType, when type is ProductRuleGroupType::ALL_PRODUCT_RULES all the product rules must be valid
        // for the whole product rule group to be valid
        // So far we only handle one product rule group (or one product segment) to match the form behaviour

        return [
            new ProductRuleGroup(
                $quantity,
                $productRules,
                ProductRuleGroupType::ALL_PRODUCT_RULES
            ),
        ];
    }

    private function filterOutProductConditions(TableNode $node): array
    {
        $rowData = $node->getRowsHash();

        // Filter out productConditions[productType] fields because they cause bugs when parsing localized row
        foreach (array_keys($rowData) as $rowKey) {
            if (str_starts_with($rowKey, 'productCondition')) {
                unset($rowData[$rowKey]);
            }
        }

        return $this->parseLocalizedRow($rowData);
    }
}
