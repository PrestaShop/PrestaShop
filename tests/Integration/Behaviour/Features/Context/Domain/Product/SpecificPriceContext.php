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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use DateTimeInterface;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\DeleteSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\CustomerInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class SpecificPriceContext extends AbstractProductFeatureContext
{
    /**
     * @Transform table:specific price detail,value
     *
     * @param TableNode $tableNode
     *
     * @return SpecificPriceForEditing
     */
    public function transformSpecificPrice(TableNode $tableNode): SpecificPriceForEditing
    {
        $dataRows = $tableNode->getRowsHash();
        $customerId = $this->getStoredId($dataRows, 'customer');

        if ($customerId) {
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->getQueryBus()->handle(new GetCustomerForEditing($customerId));

            $customerInfo = new CustomerInfo(
                $editableCustomer->getCustomerId()->getValue(),
                $editableCustomer->getFirstName()->getValue(),
                $editableCustomer->getLastName()->getValue(),
                $editableCustomer->getEmail()->getValue()
            );
        }

        return new SpecificPriceForEditing(
            42, // The ID does not matter we don't check it
            $dataRows['reduction type'],
            new DecimalNumber($dataRows['reduction value']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            InitialPrice::isInitialPriceValue($dataRows['fixed price']) ? new InitialPrice() : new FixedPrice($dataRows['fixed price']),
            (int) $dataRows['from quantity'],
            DateTimeUtil::buildNullableDateTime($dataRows['from']),
            DateTimeUtil::buildNullableDateTime($dataRows['to']),
            $this->getSharedStorage()->get($dataRows['product']),
            $customerInfo ?? null,
            $this->getStoredId($dataRows, 'combination'),
            $this->getStoredId($dataRows, 'shop'),
            $this->getStoredId($dataRows, 'currency'),
            $this->getStoredId($dataRows, 'country'),
            $this->getStoredId($dataRows, 'group')
        );
    }

    /**
     * @param TableNode $tableNode
     *
     * @return SpecificPriceList
     */
    private function transformSpecificPriceList(TableNode $tableNode): SpecificPriceList
    {
        $dataRows = $tableNode->getColumnsHash();
        $specificPrices = [];
        foreach ($dataRows as $dataRow) {
            $specificPriceId = !empty($dataRow['price id']) ? $this->getSharedStorage()->get($dataRow['price id']) : 0;
            $fixedPrice = $dataRow['fixed price'];
            $specificPrices[] = new SpecificPriceForListing(
                $specificPriceId,
                $dataRow['reduction type'],
                new DecimalNumber($dataRow['reduction value']),
                PrimitiveUtils::castStringBooleanIntoBoolean($dataRow['includes tax']),
                InitialPrice::isInitialPriceValue($fixedPrice) ? new InitialPrice() : new FixedPrice($fixedPrice),
                (int) $dataRow['from quantity'],
                DateTimeUtil::buildNullableDateTime($dataRow['from']),
                DateTimeUtil::buildNullableDateTime($dataRow['to']),
                $dataRow['combination'] ?: null,
                $dataRow['shop'] ?: null,
                $dataRow['currency'] ?: null,
                $dataRow['currencyISOCode'] ?: null,
                $dataRow['country'] ?: null,
                $dataRow['group'] ?: null,
                $dataRow['customer'] ?: null
            );
        }

        return new SpecificPriceList($specificPrices, count($dataRows));
    }

    /**
     * @When I add a specific price :specificPriceReference to product :productReference with following details:
     *
     * @param string $specificPriceReference
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function addSpecificPrice(string $specificPriceReference, string $productReference, TableNode $tableNode): void
    {
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = $this->createAddSpecificPriceCommand($productId, $tableNode);

            /** @var SpecificPriceId $specificPriceId */
            $specificPriceId = $this->getCommandBus()->handle($command);

            $this->getSharedStorage()->set($specificPriceReference, $specificPriceId->getValue());
        } catch (SpecificPriceException|DomainConstraintException|ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit specific price ":specificPriceReference" with following details:
     *
     * @param string $specificPriceReference
     * @param TableNode $tableNode
     */
    public function editSpecificPrice(string $specificPriceReference, TableNode $tableNode): void
    {
        $specificPriceId = $this->getSharedStorage()->get($specificPriceReference);
        try {
            $command = $this->createEditSpecificPriceCommand($specificPriceId, $tableNode);
            $this->getCommandBus()->handle($command);
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete specific price ":specificPriceReference"
     *
     * @param string $specificPriceReference
     */
    public function deleteSpecificPrice(string $specificPriceReference): void
    {
        $specificPriceId = $this->getSharedStorage()->get($specificPriceReference);
        try {
            $this->getCommandBus()->handle(new DeleteSpecificPriceCommand($specificPriceId));
        } catch (DomainException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have :expectedCount specific prices
     *
     * @param string $productReference
     * @param int $expectedCount
     */
    public function countProductSpecificPrices(string $productReference, int $expectedCount): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);
        /** @var SpecificPriceList $productSpecificPrices */
        $productSpecificPrices = $this->getQueryBus()->handle(new GetSpecificPriceList(
            $productId,
            $this->getDefaultLangId()
        ));

        Assert::assertEquals($expectedCount, $productSpecificPrices->getTotalSpecificPricesCount());
        Assert::assertEquals($expectedCount, count($productSpecificPrices->getSpecificPrices()));
    }

    /**
     * @Then specific price :specificPriceReference should have following details:
     *
     * @param string $specificPriceReference
     * @param SpecificPriceForEditing $expectedSpecificPrice
     *
     * @see transformSpecificPrice for TablenNode to SpecificPrice transformation
     */
    public function assertProductSpecificPrice(string $specificPriceReference, SpecificPriceForEditing $expectedSpecificPrice): void
    {
        $specificPriceId = (int) $this->getSharedStorage()->get($specificPriceReference);
        /** @var SpecificPriceForEditing $productSpecificPrice */
        $productSpecificPrice = $this->getQueryBus()->handle(new GetSpecificPriceForEditing($specificPriceId));

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $specificPricePropertyNames = [
            'reductionType', 'includesTax', 'fromQuantity', 'shopId',
            'currencyId', 'countryId', 'groupId', 'productId', 'combinationId',
        ];
        foreach ($specificPricePropertyNames as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedSpecificPrice, $propertyName),
                $propertyAccessor->getValue($productSpecificPrice, $propertyName),
                sprintf('Unexpected %s of "%s"', $propertyName, $specificPriceReference)
            );
        }

        Assert::assertEquals($expectedSpecificPrice->getCustomerInfo(), $productSpecificPrice->getCustomerInfo());

        $specificPriceDateTimeProperties = ['dateTimeFrom', 'dateTimeTo'];
        foreach ($specificPriceDateTimeProperties as $dateTimeProperty) {
            /** @var DateTimeInterface $expectedDateTime */
            $expectedDateTime = $propertyAccessor->getValue($expectedSpecificPrice, $dateTimeProperty);
            /** @var DateTimeInterface $productDateTime */
            $productDateTime = $propertyAccessor->getValue($productSpecificPrice, $dateTimeProperty);

            Assert::assertSame(
                $expectedDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                $productDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected specific price date time'
            );
        }

        $specificPriceDecimalProperties = ['reductionAmount', 'fixedPrice.value'];
        foreach ($specificPriceDecimalProperties as $decimalProperty) {
            /** @var DecimalNumber $expectedNumber */
            $expectedNumber = $propertyAccessor->getValue($expectedSpecificPrice, $decimalProperty);
            /** @var DecimalNumber $productNumber */
            $productNumber = $propertyAccessor->getValue($productSpecificPrice, $decimalProperty);
            Assert::assertTrue(
                $expectedNumber->equals($productNumber),
                sprintf(
                    'Unexpected number %s of "%s" expected %s but got %s instead',
                    $decimalProperty,
                    $specificPriceReference,
                    (string) $expectedNumber,
                    (string) $productNumber
                )
            );
        }
    }

    /**
     * @Then product ":productReference" should have following list of specific prices in ":langIso" language:
     *
     * @param string $productReference
     * @param string $langIso
     * @param TableNode $tableNode
     */
    public function assertSpecificPriceList(string $productReference, string $langIso, TableNode $tableNode): void
    {
        $langId = (int) Language::getIdByIso($langIso);
        $productId = $this->getSharedStorage()->get($productReference);
        /** @var SpecificPriceList $actualList */
        $actualList = $this->getQueryBus()->handle(new GetSpecificPriceList($productId, $langId));
        $expectedList = $this->transformSpecificPriceList($tableNode);

        Assert::assertEquals(
            $expectedList->getTotalSpecificPricesCount(),
            $actualList->getTotalSpecificPricesCount(),
            'Unexpected count of specific prices for listing'
        );

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $actualSpecificPrices = $actualList->getSpecificPrices();
        $dataRows = $tableNode->getColumnsHash();
        foreach ($expectedList->getSpecificPrices() as $key => $expectedItem) {
            $actualItem = $actualSpecificPrices[$key];
            $dataRow = $dataRows[$key];

            $scalarPropertyNames = [
                'reductionType', 'includesTax',
                'fromQuantity', 'shopName', 'currencyName', 'currencyISOCode', 'countryName',
                'groupName', 'customerName', 'combinationName',
            ];

            foreach ($scalarPropertyNames as $propertyName) {
                Assert::assertSame(
                    $propertyAccessor->getValue($expectedItem, $propertyName),
                    $propertyAccessor->getValue($actualItem, $propertyName),
                    sprintf('Unexpected specificPriceForListing "%s"', $propertyName)
                );
            }

            // If the specific price id was specified we check for its value
            if ($expectedItem->getSpecificPriceId() !== 0) {
                Assert::assertSame($expectedItem->getSpecificPriceId(), $actualItem->getSpecificPriceId());
            }
            // If the reference column was specified we assign the reference ith the matching ID
            if (!empty($dataRow['id reference'])) {
                $this->getSharedStorage()->set($dataRow['id reference'], $actualItem->getSpecificPriceId());
            }

            $decimalProperties = ['reductionValue', 'fixedPrice.value'];
            foreach ($decimalProperties as $decimalPropertyName) {
                /** @var DecimalNumber $expectedDecimal */
                $expectedDecimal = $propertyAccessor->getValue($expectedItem, $decimalPropertyName);
                /** @var DecimalNumber $actualDecimal */
                $actualDecimal = $propertyAccessor->getValue($actualItem, $decimalPropertyName);

                Assert::assertTrue(
                    $expectedDecimal->equals($actualDecimal),
                    sprintf('Unexpected specificPriceForListing "%s"', $decimalPropertyName)
                );
            }

            $dateTimeProperties = ['dateTimeFrom', 'dateTimeTo'];
            foreach ($dateTimeProperties as $dateTimeProperty) {
                /** @var DateTimeInterface $expectedDateTime */
                $expectedDateTime = $propertyAccessor->getValue($expectedItem, $dateTimeProperty);
                /** @var DateTimeInterface $productDateTime */
                $productDateTime = $propertyAccessor->getValue($actualItem, $dateTimeProperty);

                Assert::assertSame(
                    $expectedDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    $productDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    'Unexpected specific price date time'
                );
            }
        }
    }

    /**
     * @Then I should get error that specific price :fieldName is invalid
     *
     * @param string $fieldName
     */
    public function assertConstraintError(string $fieldName): void
    {
        $exceptionClass = SpecificPriceConstraintException::class;
        if (in_array($fieldName, ['reduction_type', 'reduction_amount', 'reduction_percentage'])) {
            $exceptionClass = DomainConstraintException::class;
        }
        $this->assertLastErrorIs(
            $exceptionClass,
            $this->getConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get error that identical specific price already exists for product
     */
    public function assertLastErrorIsSpecificPriceNotUnique(): void
    {
        $this->assertLastErrorIs(
            SpecificPriceConstraintException::class,
            SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT
        );
    }

    /**
     * @Then I should get error that specific price reduction or price must be set
     */
    public function assertLastErrorIsSpecificPriceReductionOrPriceMustBeSet(): void
    {
        $this->assertLastErrorIs(
            SpecificPriceConstraintException::class,
            SpecificPriceConstraintException::REDUCTION_OR_PRICE_MUST_BE_SET
        );
    }

    /**
     * @param string $fieldName
     *
     * @return int
     */
    private function getConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'from_quantity' => SpecificPriceConstraintException::INVALID_FROM_QUANTITY,
            'tax_included' => SpecificPriceConstraintException::INVALID_TAX_INCLUDED,
            'reduction_amount' => DomainConstraintException::INVALID_REDUCTION_AMOUNT,
            'reduction_percentage' => DomainConstraintException::INVALID_REDUCTION_PERCENTAGE,
            'reduction_type' => DomainConstraintException::INVALID_REDUCTION_TYPE,
            'fixed price' => SpecificPriceConstraintException::INVALID_FIXED_PRICE,
            'from' => SpecificPriceConstraintException::INVALID_FROM_DATETIME,
            'to' => SpecificPriceConstraintException::INVALID_TO_DATETIME,
            'date range' => SpecificPriceConstraintException::INVALID_DATE_RANGE,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    /**
     * @param int $productId
     * @param TableNode $tableNode
     *
     * @return AddSpecificPriceCommand
     *
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     */
    private function createAddSpecificPriceCommand(int $productId, TableNode $tableNode): AddSpecificPriceCommand
    {
        $dataRows = $tableNode->getRowsHash();
        $addCommand = new AddSpecificPriceCommand(
            $productId,
            $dataRows['reduction type'],
            $dataRows['reduction value'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            $dataRows['fixed price'],
            (int) $dataRows['from quantity'],
            DateTimeUtil::buildNullableDateTime($dataRows['from'] ?? null),
            DateTimeUtil::buildNullableDateTime($dataRows['to'] ?? null)
        );

        if (!empty($dataRows['combination'])) {
            $addCommand->setCombinationId($this->getStoredId($dataRows, 'combination'));
        }
        if (!empty($dataRows['shop'])) {
            $addCommand->setShopId($this->getStoredId($dataRows, 'shop'));
        }
        if (!empty($dataRows['currency'])) {
            $addCommand->setCurrencyId($this->getStoredId($dataRows, 'currency'));
        }
        if (!empty($dataRows['country'])) {
            $addCommand->setCountryId($this->getStoredId($dataRows, 'country'));
        }
        if (!empty($dataRows['group'])) {
            $addCommand->setGroupId($this->getStoredId($dataRows, 'group'));
        }
        if (!empty($dataRows['customer'])) {
            $addCommand->setCustomerId($this->getStoredId($dataRows, 'customer'));
        }
        if (!empty($dataRows['from'])) {
            $addCommand->setDateTimeFrom(new DateTime($dataRows['from']));
        }
        if (!empty($dataRows['to'])) {
            $addCommand->setDateTimeTo(new DateTime($dataRows['to']));
        }

        return $addCommand;
    }

    /**
     * @param int $specificPriceId
     * @param TableNode $tableNode
     *
     * @return EditSpecificPriceCommand
     */
    private function createEditSpecificPriceCommand(int $specificPriceId, TableNode $tableNode): EditSpecificPriceCommand
    {
        $dataRows = $tableNode->getRowsHash();
        $editCommand = new EditSpecificPriceCommand($specificPriceId);

        if (isset($dataRows['reduction type'], $dataRows['reduction value'])) {
            $editCommand->setReduction($dataRows['reduction type'], (string) $dataRows['reduction value']);
        }
        if (isset($dataRows['includes tax'])) {
            $editCommand->setIncludesTax(PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']));
        }
        if (isset($dataRows['fixed price'])) {
            $editCommand->setFixedPrice($dataRows['fixed price']);
        }
        if (isset($dataRows['from quantity'])) {
            $editCommand->setFromQuantity((int) $dataRows['from quantity']);
        }
        if (isset($dataRows['combination'])) {
            $editCommand->setCombinationId($this->getStoredId($dataRows, 'combination'));
        }
        if (isset($dataRows['shop'])) {
            $editCommand->setShopId($this->getNullableIdForEdit($dataRows, 'shop'));
        }
        if (isset($dataRows['currency'])) {
            $editCommand->setCurrencyId($this->getNullableIdForEdit($dataRows, 'currency'));
        }
        if (isset($dataRows['country'])) {
            $editCommand->setCountryId($this->getNullableIdForEdit($dataRows, 'country'));
        }
        if (isset($dataRows['group'])) {
            $editCommand->setGroupId($this->getNullableIdForEdit($dataRows, 'group'));
        }
        if (isset($dataRows['customer'])) {
            $editCommand->setCustomerId($this->getNullableIdForEdit($dataRows, 'customer'));
        }
        if (isset($dataRows['from'])) {
            $editCommand->setDateTimeFrom($this->getDateTime($dataRows['from']));
        }
        if (isset($dataRows['to'])) {
            $editCommand->setDateTimeTo($this->getDateTime($dataRows['to']));
        }

        return $editCommand;
    }

    /**
     * @param string $input
     *
     * @return DateTimeInterface
     */
    private function getDateTime(string $input): DateTimeInterface
    {
        return DateTimeUtil::buildNullableDateTime($input);
    }

    /**
     * When editing the id fields can be 0 (if we leave it empty it acts as reset action)
     *
     * @param array $dataRows
     * @param string $fieldId
     *
     * @return int
     */
    private function getNullableIdForEdit(array $dataRows, string $fieldId): int
    {
        if (empty($dataRows[$fieldId])) {
            return 0;
        }

        return $this->getStoredId($dataRows, $fieldId);
    }

    /**
     * @param array $dataRows
     * @param string $fieldId
     *
     * @return int|null
     */
    private function getStoredId(array $dataRows, string $fieldId): ?int
    {
        if (empty($dataRows[$fieldId])) {
            return null;
        }

        if (ctype_digit($dataRows[$fieldId])) {
            return (int) $dataRows[$fieldId];
        }

        if (!$this->getSharedStorage()->exists($dataRows[$fieldId])) {
            throw new RuntimeException(sprintf('Trying to access a non saved id by key %s', $dataRows[$fieldId]));
        }

        return (int) $this->getSharedStorage()->get($dataRows[$fieldId]);
    }
}
