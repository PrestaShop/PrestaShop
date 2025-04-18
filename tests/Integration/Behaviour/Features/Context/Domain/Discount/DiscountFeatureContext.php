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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Discount;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddCartLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeGiftDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeShippingDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddOrderLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddProductLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class DiscountFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create a free shipping discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     */
    public function createFreeShippingDiscount(string $discountReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddFreeShippingDiscountCommand();
            $this->createDiscount($discountReference, $data, $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a cart level discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     *
     * @return void
     *
     * @throws Exception
     */
    public function createCartLevelDiscountIfNotExists(string $discountReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddCartLevelDiscountCommand();
            $this->createDiscount($discountReference, $data, $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a cart level discount :discountReference
     *
     * @param string $discountReference
     *
     * @return void
     *
     * @throws Exception
     */
    public function createSimpleCartLevelDiscountIfNotExists(string $discountReference): void
    {
        try {
            $command = new AddCartLevelDiscountCommand();
            $this->createDiscount($discountReference, [], $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a order level discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     *
     * @return void
     *
     * @throws Exception
     */
    public function createOrderLevelDiscountIfNotExists(string $discountReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddOrderLevelDiscountCommand();
            $this->createDiscount($discountReference, $data, $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a order level discount :discountReference
     *
     * @param string $discountReference
     *
     * @return void
     *
     * @throws Exception
     */
    public function createSimpleOrderLevelDiscountIfNotExists(string $discountReference): void
    {
        try {
            $command = new AddOrderLevelDiscountCommand();
            $this->createDiscount($discountReference, [], $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a product level discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     *
     * @return void
     *
     * @throws Exception
     */
    public function createProductLevelDiscountIfNotExists(string $discountReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddProductLevelDiscountCommand();
            $this->createDiscount($discountReference, $data, $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a product level discount :discountReference
     *
     * @param string $discountReference
     *
     * @return void
     *
     * @throws Exception
     */
    public function createSimpleProductLevelDiscountIfNotExists(string $discountReference): void
    {
        try {
            $command = new AddProductLevelDiscountCommand();
            $this->createDiscount($discountReference, [], $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a free shipping discount :discountReference
     *
     * @param string $discountReference
     */
    public function createFreeShippingDiscountWithNoParameters(string $discountReference): void
    {
        try {
            $command = new AddFreeShippingDiscountCommand();
            $this->createDiscount($discountReference, [], $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a free gift discount :discountReference
     *
     * @param string $discountReference
     */
    public function createFreeGiftDiscountWithNoParameters(string $discountReference): void
    {
        try {
            $command = new AddFreeGiftDiscountCommand();
            $this->createDiscount($discountReference, [], $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create a free gift discount :discountReference with following properties:
     *
     * @param string $discountReference
     * @param TableNode $node
     *
     * @return void
     *
     * @throws Exception
     */
    public function createFreeGiftDiscountIfNotExists(string $discountReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddFreeGiftDiscountCommand();
            $this->createDiscount($discountReference, $data, $command);
        } catch (DiscountConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that discount field :field is invalid
     */
    public function assertDiscountInvalidField(string $field): void
    {
        $errorCode = match ($field) {
            'name' => DiscountConstraintException::INVALID_NAME,
            default => null,
        };

        $this->assertLastErrorIs(DiscountConstraintException::class, $errorCode);
    }

    /**
     * @Then I should get an error that the discount is invalid
     */
    public function assertDiscountIsInvalid(): void
    {
        $this->assertLastErrorIs(CartRuleValidityException::class);
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
            // if discount already exists we assert all its expected properties
            $this->assertDiscountProperties(
                $this->getDiscountForEditing($discountReference),
                $this->localizeByRows($tableNode)
            );
        } catch (DiscountException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @throws DiscountConstraintException
     * @throws Exception
     */
    protected function createDiscount(string $cartRuleReference, array $data, AddDiscountCommand $command): void
    {
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

        $command->setDescription($data['description'] ?? '');
        if (!empty($data['code'])) {
            $command->setCode($data['code']);
        }

        if ($command instanceof AddCartLevelDiscountCommand
            || $command instanceof AddProductLevelDiscountCommand
            || $command instanceof AddOrderLevelDiscountCommand
        ) {
            if (!empty($data['reduction_percent'])) {
                $command->setPercentDiscount(new DecimalNumber($data['reduction_percent']));
            }

            if (!empty($data['reduction_amount'])) {
                $command->setAmountDiscount(
                    new DecimalNumber($data['reduction_amount']),
                    new CurrencyId($this->getSharedStorage()->get($data['reduction_currency'])),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['taxIncluded']),
                );
            }
        }

        if ($command instanceof AddProductLevelDiscountCommand) {
            if (!empty($data['reduction_product'])) {
                if ((int) $data['reduction_product'] === -1 || (int) $data['reduction_product'] === -2) {
                    $command->setReductionProduct((int) $data['reduction_product']);
                } else {
                    $command->setReductionProduct($this->getSharedStorage()->get($data['reduction_product']));
                }
            }
        }

        if ($command instanceof AddFreeGiftDiscountCommand) {
            if (!empty($data['gift_product'])) {
                $command->setProductId($this->referenceToId($data['gift_product']));
            }

            if (!empty($data['gift_combination'])) {
                $command->setCombinationId($this->referenceToId($data['gift_combination']));
            }
        }

        /** @var DiscountId $discountId */
        $discountId = $this->getCommandBus()->handle($command);
        $this->getSharedStorage()->set($cartRuleReference, $discountId->getValue());
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
            Assert::assertSame(
                !empty($expectedData['customer']) ? $this->getSharedStorage()->get($expectedData['customer']) : 0,
                $discountForEditing->getCustomerId(),
                'Unexpected customer id'
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
            Assert::assertSame((int) $expectedData['total_quantity'], $discountForEditing->getTotalQuantity(), 'Unexpected quantity');
        }
        if (isset($expectedData['quantity_per_user'])) {
            Assert::assertSame((int) $expectedData['quantity_per_user'], $discountForEditing->getQuantityPerUser(), 'Unexpected quantity_per_user');
        }

        if (isset($expectedData['reduction_percent'])) {
            Assert::assertSame((float) $expectedData['reduction_percent'], (float) (string) $discountForEditing->getPercentDiscount(), 'Unexpected percent discount');
        }

        if (isset($expectedData['reduction_amount'])) {
            Assert::assertSame((float) $expectedData['reduction_amount'], (float) (string) $discountForEditing->getAmountDiscount(), 'Unexpected amount discount');
        }
        if (isset($expectedData['reduction_currency'])) {
            Assert::assertSame($this->getSharedStorage()->get($expectedData['reduction_currency']), $discountForEditing->getCurrencyId(), 'Unexpected reduction currency');
        }
        if (isset($expectedData['taxIncluded'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['taxIncluded']), $discountForEditing->isTaxIncluded(), 'Unexpected tax included');
        }
        if (isset($expectedData['type'])) {
            Assert::assertSame($expectedData['type'], $discountForEditing->getType()->getValue(), 'Unexpected type');
        }
        if (isset($expectedData['reduction_product'])) {
            if ((int) $expectedData['reduction_product'] === -1 || (int) $expectedData['reduction_product'] === -2) {
                Assert::assertSame((int) $expectedData['reduction_product'], $discountForEditing->getReductionProduct());
            } else {
                Assert::assertSame($this->getSharedStorage()->get($expectedData['reduction_product']), $discountForEditing->getReductionProduct());
            }
        }
    }

    protected function getDiscountForEditing(string $discountReference): DiscountForEditing
    {
        /** @var DiscountForEditing $discountForEditing */
        $discountForEditing = $this->getQueryBus()->handle(
            new GetDiscountForEditing($this->getSharedStorage()->get($discountReference))
        );

        return $discountForEditing;
    }
}
