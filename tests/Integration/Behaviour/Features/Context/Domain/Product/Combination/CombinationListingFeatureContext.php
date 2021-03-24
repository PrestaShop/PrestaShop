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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationFromListingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\EditableCombinationForListing;
use PrestaShop\PrestaShop\Core\Search\Filters\CombinationFilters;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CombinationListingFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update combination :combinationReference from list with following values:
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updateCombinationFromListing(string $combinationReference, TableNode $tableNode): void
    {
        $command = new UpdateCombinationFromListingCommand($this->getSharedStorage()->get($combinationReference));
        $this->fillCommand($command, $tableNode->getRowsHash());

        $this->getCommandBus()->handle($command);
    }

    /**
     * @param UpdateCombinationFromListingCommand $command
     * @param array<string, string> $dataRows
     */
    private function fillCommand(UpdateCombinationFromListingCommand $command, array $dataRows): void
    {
        if (isset($dataRows['impact on price'])) {
            $command->setImpactOnPrice($dataRows['impact on price']);
        }
        if (isset($dataRows['quantity'])) {
            $command->setQuantity((int) $dataRows['quantity']);
        }
        if (isset($dataRows['is default'])) {
            $command->setDefault(PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['is default']));
        }
        if (isset($dataRows['combination reference'])) {
            $command->setReference($dataRows['combination reference']);
        }
    }

    /**
     * @Then I should see following combinations list of product ":productReference":
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertCombinationsList(string $productReference, TableNode $tableNode): void
    {
        $this->assertCombinations($productReference, $tableNode->getColumnsHash());
    }

    /**
     * @Then combinations list of product ":productReference" should be empty
     *
     * @param string $productReference
     */
    public function assertNoCombinationsInPage(string $productReference): void
    {
        $this->assertCombinations($productReference, []);
    }

    /**
     * @Given product ":productReference" combinations list search criteria is reset to defaults
     *
     * @param string $productReference
     */
    public function cleanSearchCriteria(string $productReference): void
    {
        $this->getSharedStorage()->clear($this->getSearchCriteriaKey($productReference));
    }

    /**
     * @Transform table:criteria,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationFilters
     */
    public function transformCombinationSearchCriteria(TableNode $tableNode): CombinationFilters
    {
        $dataRows = $tableNode->getRowsHash();
        $defaults = CombinationFilters::getDefaults();

        $filters = $defaults['filters'];
        if (isset($dataRows['attributes'])) {
            $attributes = PrimitiveUtils::castStringArrayIntoArray($dataRows['attributes']);
            foreach ($attributes as $attributeRef) {
                $filters['attribute_ids'][] = $this->getSharedStorage()->get($attributeRef);
            }
        }

        if (isset($dataRows['combination reference'])) {
            $filters[$this->getDbField('combination reference')] = $dataRows['combination reference'];
        }

        if (isset($dataRows['is default'])) {
            $filters[$this->getDbField('is default')] = PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['is default']);
        }

        $limit = isset($dataRows['limit']) ? (int) $dataRows['limit'] : $defaults['limit'];
        $offset = isset($dataRows['page']) ? $this->countOffset((int) $dataRows['page'], $limit) : $defaults['offset'];
        $orderBy = isset($dataRows['order by']) ? $this->getDbField($dataRows['order by']) : $defaults['orderBy'];
        $orderWay = isset($dataRows['order way']) ? $this->getDbField($dataRows['order way']) : $defaults['sortOrder'];

        return new CombinationFilters([
            'limit' => $limit,
            'offset' => $offset,
            'orderBy' => $orderBy,
            'sortOrder' => $orderWay,
            'filters' => $filters,
        ]);
    }

    /**
     * @param string $field
     *
     * @return string
     */
    private function getDbField(string $field): string
    {
        $fieldMap = [
            'combination reference' => 'reference',
            'impact on price' => 'price',
            'is default' => 'default_on',
        ];

        if (isset($fieldMap[$field])) {
            return $fieldMap[$field];
        }

        return $field;
    }

    /**
     * @When I search product ":productReference" combinations list by following search criteria:
     *
     * @param string $productReference
     * @param CombinationFilters $combinationFilters
     */
    public function storeSearchCriteria(string $productReference, CombinationFilters $combinationFilters): void
    {
        $this->getSharedStorage()->set($this->getSearchCriteriaKey($productReference), $combinationFilters);
    }

    /**
     * @param string $productReference
     * @param array $dataRows
     */
    private function assertCombinations(string $productReference, array $dataRows): void
    {
        $searchCriteriaKey = $this->getSearchCriteriaKey($productReference);
        if ($this->getSharedStorage()->exists($searchCriteriaKey)) {
            $combinationFilters = $this->getSharedStorage()->get($searchCriteriaKey);
        }

        $combinationsList = $this->getCombinationsList(
            $productReference,
            isset($combinationFilters) ? $combinationFilters : CombinationFilters::buildDefaults()
        );

        Assert::assertEquals(
            count($dataRows),
            count($combinationsList->getCombinations()),
            'Unexpected combinations count'
        );

        $idsByReference = $this->assertListedCombinationsProperties($dataRows, $combinationsList->getCombinations());

        foreach ($idsByReference as $reference => $id) {
            $this->getSharedStorage()->set($reference, $id);
        }
    }

    /**
     * Asserts expected product combinations and sets combination references in shared storage
     *
     * @param string $productReference
     * @param TableNode $table
     */
    private function assertWholeCombinationsList(string $productReference, TableNode $table): void
    {
        /** @var CombinationFilters $combinationFilters */
        $combinationFilters = CombinationFilters::buildDefaults();
        $combinationsList = $this->getCombinationsList($productReference, $combinationFilters);
        $dataRows = $table->getColumnsHash();

        Assert::assertEquals(
            count($dataRows),
            $combinationsList->getTotalCombinationsCount(),
            'Unexpected combinations count'
        );
    }

    /**
     * @Then product :productReference should have no combinations
     *
     * @param string $productReference
     */
    public function assertNoCombinations(string $productReference): void
    {
        $combinationsList = $this->getCombinationsList($productReference);

        Assert::assertEmpty($combinationsList->getCombinations());
        Assert::assertEquals(0, $combinationsList->getTotalCombinationsCount());
    }

    /**
     * @param string $productReference
     *
     * @return string
     */
    private function getSearchCriteriaKey(string $productReference): string
    {
        return sprintf('combination_search_criteria_%s', $productReference);
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return int
     */
    private function countOffset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    /**
     * @param array $expectedDataRows
     * @param EditableCombinationForListing[] $listCombinations
     *
     * @return array<string, int> combinations [reference => id] list
     */
    private function assertListedCombinationsProperties(array $expectedDataRows, array $listCombinations): array
    {
        $idsByReference = [];
        foreach ($listCombinations as $key => $editableCombinationForListing) {
            $expectedCombination = $expectedDataRows[$key];

            Assert::assertSame(
                $expectedCombination['combination name'],
                $editableCombinationForListing->getCombinationName(),
                'Unexpected combination name'
            );
            Assert::assertSame(
                $expectedCombination['combination reference'],
                $editableCombinationForListing->getReference(),
                'Unexpected combination reference'
            );
            Assert::assertSame(
                PrimitiveUtils::castStringBooleanIntoBoolean($expectedCombination['is default']),
                $editableCombinationForListing->isDefault(),
                'Unexpected default combination'
            );
            Assert::assertEquals(
                $expectedCombination['impact on price'],
                (string) $editableCombinationForListing->getImpactOnPrice(),
                'Unexpected combination impact on price'
            );
            Assert::assertSame(
                (int) $expectedCombination['quantity'],
                $editableCombinationForListing->getQuantity(),
                'Unexpected combination quantity'
            );

            $expectedAttributesInfo = $this->parseAttributesInfo($expectedCombination['attributes']);
            Assert::assertSame(
                count($expectedAttributesInfo),
                count($editableCombinationForListing->getAttributesInformation()),
                'Unexpected attributes count in combination'
            );

            $this->assertAttributesInfo($expectedAttributesInfo, $editableCombinationForListing->getAttributesInformation());

            $idsByReference[$expectedCombination['reference']] = $editableCombinationForListing->getCombinationId();
        }

        return $idsByReference;
    }

    /**
     * @param CombinationAttributeInformation[] $expectedAttributesInfo
     * @param CombinationAttributeInformation[] $attributesInfo
     */
    private function assertAttributesInfo(array $expectedAttributesInfo, array $attributesInfo): void
    {
        foreach ($attributesInfo as $index => $actualAttributesInfo) {
            Assert::assertSame(
                $actualAttributesInfo->getAttributeGroupId(),
                $expectedAttributesInfo[$index]->getAttributeGroupId(),
                'Unexpected attribute group id'
            );
            Assert::assertSame(
                $actualAttributesInfo->getAttributeGroupName(),
                $expectedAttributesInfo[$index]->getAttributeGroupName(),
                'Unexpected attribute group name'
            );
            Assert::assertSame(
                $actualAttributesInfo->getAttributeId(),
                $expectedAttributesInfo[$index]->getAttributeId(),
                'Unexpected attribute id'
            );
            Assert::assertSame(
                $actualAttributesInfo->getAttributeName(),
                $expectedAttributesInfo[$index]->getAttributeName(),
                'Unexpected attribute name'
            );
        }
    }

    /**
     * @param string $combinationDataRow
     *
     * @return CombinationAttributeInformation[]
     */
    private function parseAttributesInfo(string $combinationDataRow): array
    {
        $combinationDataRow = PrimitiveUtils::castStringArrayIntoArray($combinationDataRow);
        $combinationAttributesInfo = [];
        foreach ($combinationDataRow as $attributesInfo) {
            $attributeInfo = explode(':', $attributesInfo);
            $combinationAttributesInfo[] = new CombinationAttributeInformation(
                $this->getSharedStorage()->get($attributeInfo[0]),
                $attributeInfo[0],
                $this->getSharedStorage()->get($attributeInfo[1]),
                $attributeInfo[1]
            );
        }

        return $combinationAttributesInfo;
    }
}
