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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddProductSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditProductSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetEditableSpecificPricesList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceListForEditing;
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

        return new SpecificPriceForEditing(
            42, // The ID does not matter we don't check it
            $dataRows['reduction type'],
            new DecimalNumber($dataRows['reduction value']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            new DecimalNumber($dataRows['price']),
            (int) $dataRows['from quantity'],
            DateTimeUtil::buildNullableDateTime($dataRows['from']),
            DateTimeUtil::buildNullableDateTime($dataRows['to']),
            $this->getStoredId($dataRows, 'shop'),
            $this->getStoredId($dataRows, 'currency'),
            $this->getStoredId($dataRows, 'country'),
            $this->getStoredId($dataRows, 'group'),
            $this->getStoredId($dataRows, 'customer')
        );
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

        $this->cleanLastException();
        try {
            $command = $this->createAddSpecificPriceCommand($productId, $tableNode);

            /** @var SpecificPriceId $specificPriceId */
            $specificPriceId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($specificPriceReference, $specificPriceId->getValue());
        } catch (SpecificPriceException | DomainConstraintException | ProductException $e) {
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
     * @Then product :productReference should have :expectedCount specific prices
     *
     * @param string $productReference
     * @param int $expectedCount
     */
    public function countProductSpecificPrices(string $productReference, int $expectedCount): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);
        /** @var SpecificPriceListForEditing $productSpecificPrices */
        $productSpecificPrices = $this->getQueryBus()->handle(new GetEditableSpecificPricesList($productId));

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
        $productSpecificPrice = $this->getQueryBus()->handle(new GetSpecificPriceForEditing($specificPriceId));

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $specificPricePropertyNames = [
            'reductionType', 'includesTax', 'fromQuantity',
            'shopId', 'currencyId', 'countryId', 'groupId', 'customerId',
        ];
        foreach ($specificPricePropertyNames as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedSpecificPrice, $propertyName),
                $propertyAccessor->getValue($productSpecificPrice, $propertyName),
                sprintf('Unexpected %s of "%s"', $propertyName, $specificPriceReference)
            );
        }

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

        $specificPriceDecimalProperties = ['reductionAmount', 'price'];
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
            'price' => SpecificPriceConstraintException::INVALID_PRICE,
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
     * @return AddProductSpecificPriceCommand
     *
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     */
    private function createAddSpecificPriceCommand(int $productId, TableNode $tableNode): AddProductSpecificPriceCommand
    {
        $dataRows = $tableNode->getRowsHash();
        $addCommand = new AddProductSpecificPriceCommand(
            $productId,
            $dataRows['reduction type'],
            $dataRows['reduction value'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            $dataRows['price'],
            (int) $dataRows['from quantity']
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
     * @return EditProductSpecificPriceCommand
     */
    private function createEditSpecificPriceCommand(int $specificPriceId, TableNode $tableNode): EditProductSpecificPriceCommand
    {
        $dataRows = $tableNode->getRowsHash();
        $editCommand = new EditProductSpecificPriceCommand($specificPriceId);

        if (isset($dataRows['reduction type'], $dataRows['reduction value'])) {
            $editCommand->setReduction($dataRows['reduction type'], (string) $dataRows['reduction value']);
        }
        if (isset($dataRows['includes tax'])) {
            $editCommand->setIncludesTax(PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']));
        }
        if (isset($dataRows['price'])) {
            $editCommand->setPrice($dataRows['price']);
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
            throw new RuntimeException(sprintf('Trying to access to non saved id with key %s', $dataRows[$fieldId]));
        }

        return (int) $this->getSharedStorage()->get($dataRows[$fieldId]);
    }
}
