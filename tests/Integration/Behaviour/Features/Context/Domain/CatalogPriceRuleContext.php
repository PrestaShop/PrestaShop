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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Country;
use Currency;
use DateTimeInterface;
use Group;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\AddCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleForListing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Shop;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CatalogPriceRuleContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add catalog price rule :catalogPriceRuleReference with following details:
     *
     * @param string $catalogPriceRuleReference
     * @param TableNode $tableNode
     */
    public function addCatalogPriceRule(string $catalogPriceRuleReference, TableNode $tableNode): void
    {
        try {
            $command = $this->createAddCatalogPriceRuleCommand($tableNode);

            /** @var CatalogPriceRuleId $catalogPriceRuleId */
            $catalogPriceRuleId = $this->getCommandBus()->handle($command);

            $this->getSharedStorage()->set($catalogPriceRuleReference, $catalogPriceRuleId->getValue());
        } catch (CatalogPriceRuleException | DomainConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then catalog price rule :catalogPriceRuleReference should have following details:
     *
     * @param string $catalogPriceRuleReference
     * @param EditableCatalogPriceRule $catalogPriceRule
     *
     * @see transformCatalogPriceRule
     */
    public function assertCatalogPriceRule(string $catalogPriceRuleReference, EditableCatalogPriceRule $expectedItem): void
    {
        $catalogPriceRuleId = $this->getSharedStorage()->get($catalogPriceRuleReference);
        /** @var EditableCatalogPriceRule $actualItem */
        $actualItem = $this->getQueryBus()->handle(new GetCatalogPriceRuleForEditing($catalogPriceRuleId));
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $scalarPropertyNames = ['name', 'shopId', 'currencyId', 'countryId', 'groupId', 'fromQuantity'];

        foreach ($scalarPropertyNames as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedItem, $propertyName),
                $propertyAccessor->getValue($actualItem, $propertyName),
                sprintf('Unexpected catalogPriceRuleForListing "%s"', $propertyName)
            );
        }

        /* isTaxIncluded can't be accessed by property accessor */
        Assert::assertSame(
            $expectedItem->isTaxIncluded(),
            $actualItem->isTaxIncluded(),
            sprintf('Unexpected catalogPriceRuleForListing "%s"', $propertyName)
        );

        $decimalProperties = ['price'];
        foreach ($decimalProperties as $decimalPropertyName) {
            /** @var DecimalNumber $expectedDecimal */
            $expectedDecimal = $propertyAccessor->getValue($expectedItem, $decimalPropertyName);
            /** @var DecimalNumber $actualDecimal */
            $actualDecimal = $propertyAccessor->getValue($actualItem, $decimalPropertyName);

            Assert::assertTrue(
                $expectedDecimal->equals($actualDecimal),
                sprintf('Unexpected catalogPriceRuleForListing "%s"', $decimalPropertyName)
            );
        }

        $dateTimeProperties = ['from', 'to'];
        foreach ($dateTimeProperties as $dateTimeProperty) {
            /** @var DateTimeInterface $expectedDateTime */
            $expectedDateTime = $propertyAccessor->getValue($expectedItem, $dateTimeProperty);
            /** @var DateTimeInterface $actualDateTime */
            $actualDateTime = $propertyAccessor->getValue($actualItem, $dateTimeProperty);
            if ($expectedDateTime === null) {
                Assert::assertSame(
                    $expectedDateTime,
                    $actualDateTime,
                    'Unexpected catalog price rule date time'
                );
                continue;
            }
            Assert::assertSame(
                $expectedDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                $actualDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'Unexpected catalog price rule date time'
            );
        }
    }

    /**
     * @Then I should be able to see following list of catalog price rules in product page for language :langIso:
     *
     * @param TableNode $expectedList
     *
     * @see transformCatalogPriceRuleList
     */
    public function assertCatalogPriceRuleList(string $langIso, TableNode $expectedList): void
    {
        $langId = (int) Language::getIdByIso($langIso);

        /** @var CatalogPriceRuleList $actualList */
        $actualList = $this->getQueryBus()->handle(
            new GetCatalogPriceRuleList(
                $langId
            )
        );

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $transformedList = $this->transformCatalogPriceRuleList($expectedList, $langId);
        Assert::assertEquals(
            $transformedList->getTotalCatalogPriceRulesCount(),
            $actualList->getTotalCatalogPriceRulesCount(),
            'Unexpected count of catalog price rules for listing'
        );
        $actualCatalogPriceRules = $actualList->getCatalogPriceRules();
        foreach ($transformedList->getCatalogPriceRules() as $key => $expectedItem) {
            $actualItem = $actualCatalogPriceRules[$key];

            $scalarPropertyNames = ['catalogPriceRuleName', 'currencyName', 'countryName', 'groupName', 'fromQuantity', 'reductionType', 'shopName'];

            foreach ($scalarPropertyNames as $propertyName) {
                Assert::assertSame(
                    $propertyAccessor->getValue($expectedItem, $propertyName),
                    $propertyAccessor->getValue($actualItem, $propertyName),
                    sprintf('Unexpected catalogPriceRuleForListing "%s"', $propertyName)
                );
            }

            $decimalProperties = ['reduction'];
            foreach ($decimalProperties as $decimalPropertyName) {
                /** @var DecimalNumber $expectedDecimal */
                $expectedDecimal = $propertyAccessor->getValue($expectedItem, $decimalPropertyName);
                /** @var DecimalNumber $actualDecimal */
                $actualDecimal = $propertyAccessor->getValue($actualItem, $decimalPropertyName);

                Assert::assertTrue(
                    $expectedDecimal->equals($actualDecimal),
                    sprintf('Unexpected catalogPriceRuleForListing "%s"', $decimalPropertyName)
                );
            }

            $dateTimeProperties = ['dateStart', 'dateEnd'];
            foreach ($dateTimeProperties as $dateTimeProperty) {
                /** @var DateTimeInterface $expectedDateTime */
                $expectedDateTime = $propertyAccessor->getValue($expectedItem, $dateTimeProperty);
                /** @var DateTimeInterface $actualDateTime */
                $actualDateTime = $propertyAccessor->getValue($actualItem, $dateTimeProperty);

                Assert::assertSame(
                    $expectedDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    $actualDateTime->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    'Unexpected catalog price rule date time'
                );
            }
        }
    }

    /**
     * @param TableNode $tableNode
     *
     * @return AddCatalogPriceRuleCommand
     */
    private function createAddCatalogPriceRuleCommand(TableNode $tableNode): AddCatalogPriceRuleCommand
    {
        $dataRows = $tableNode->getRowsHash();
        $addCommand = new AddCatalogPriceRuleCommand(
            $dataRows['name'],
            $this->getStoredId($dataRows, 'currency'),
            $this->getStoredId($dataRows, 'country'),
            $this->getStoredId($dataRows, 'group'),
            (int) $dataRows['from quantity'],
            $dataRows['reduction type'],
            $dataRows['reduction value'],
            $this->getStoredId($dataRows, 'shop'),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            (float) $dataRows['price']
        );

        return $addCommand;
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

    /**
     * @Transform table:catalog price rule detail,value
     *
     * @param TableNode $tableNode
     *
     * @return EditableCatalogPriceRule
     */
    public function transformCatalogPriceRule(TableNode $tableNode): EditableCatalogPriceRule
    {
        $dataRows = $tableNode->getRowsHash();

        return new EditableCatalogPriceRule(
            new CatalogPriceRuleId(42),
            $dataRows['name'],
            $this->getStoredId($dataRows, 'shop'),
            $this->getStoredId($dataRows, 'currency'),
            $this->getStoredId($dataRows, 'country'),
            $this->getStoredId($dataRows, 'group'),
            (int) $dataRows['from quantity'],
            new DecimalNumber($dataRows['price']),
            new Reduction($dataRows['reduction type'], $dataRows['reduction value']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['includes tax']),
            null,
            null
        );
    }

    /**
     * @param TableNode $tableNode
     *
     * @return CatalogPriceRuleList
     */
    public function transformCatalogPriceRuleList(TableNode $tableNode, int $languageId): CatalogPriceRuleList
    {
        $dataRows = $tableNode->getColumnsHash();
        $catalogPriceRules = [];
        foreach ($dataRows as $dataRow) {
            $catalogPriceRulId = $this->getSharedStorage()->get($dataRow['catalog price rule reference']);
            $shop = new Shop($this->getStoredId($dataRow, 'shop'), $languageId);
            $currency = new Currency($this->getStoredId($dataRow, 'currency'), $languageId);
            $country = new Country($this->getStoredId($dataRow, 'country'), $languageId);
            $group = new Group($this->getStoredId($dataRow, 'group'), $languageId);
            $catalogPriceRules[] = new CatalogPriceRuleForListing(
                $catalogPriceRulId,
                $dataRow['name'],
                (int) $dataRow['from quantity'],
                $dataRow['reduction type'],
                new DecimalNumber($dataRow['reduction value']),
                DateTimeUtil::buildNullableDateTime($dataRow['from']),
                DateTimeUtil::buildNullableDateTime($dataRow['to']),
                $shop->name,
                $currency->symbol,
                $country->name,
                $group->name
            );
        }

        return new CatalogPriceRuleList($catalogPriceRules, count($dataRows));
    }
}
