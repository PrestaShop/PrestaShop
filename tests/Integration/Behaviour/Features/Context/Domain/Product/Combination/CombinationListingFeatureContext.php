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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\EditableCombinationForListing;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CombinationListingFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @Then I should see following combinations of product :productReference in page :page limited to maximum :limit per page:
     *
     * @param string $productReference
     * @param int $page
     * @param TableNode $dataTable
     * @param int $limit
     */
    public function assertCombinationsPage(string $productReference, int $page, TableNode $dataTable, int $limit): void
    {
        $offset = $this->countOffset($page, $limit);

        $this->assertPaginatedCombinationList($productReference, $dataTable->getColumnsHash(), $limit, $offset);
    }

    /**
     * @Then there should be no combinations of :productReference in page :page when limited to maximum :limit per page
     *
     * @param string $productReference
     * @param int $page
     * @param int $limit
     */
    public function assertNoCombinationsInPage(string $productReference, int $page, int $limit): void
    {
        $offset = $this->countOffset($page, $limit);

        $this->assertPaginatedCombinationList($productReference, [], $limit, $offset);
    }

    /**
     * Asserts expected product combinations and sets combination references in shared storage
     *
     * @Then product :productReference should have following list of combinations:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertWholeCombinationsList(string $productReference, TableNode $table): void
    {
        $combinationsList = $this->getCombinationsList($productReference);
        $dataRows = $table->getColumnsHash();

        Assert::assertEquals(
            count($dataRows),
            $combinationsList->getTotalCombinationsCount(),
            'Unexpected combinations count'
        );

        $idsByReference = $this->assertListedCombinationsProperties($dataRows, $combinationsList->getCombinations());

        foreach ($idsByReference as $reference => $id) {
            $this->getSharedStorage()->set($reference, $id);
        }
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return int
     */
    private function countOffset(int $page, int $limit): int
    {
        return (1 === $page) ? 0 : ($page - 1) * $limit;
    }

    /**
     * @param string $productReference
     * @param array $dataRows
     * @param int|null $limit
     * @param int|null $offset
     */
    private function assertPaginatedCombinationList(string $productReference, array $dataRows, ?int $limit = null, ?int $offset = null): void
    {
        $combinationsList = $this->getCombinationsList($productReference, $limit, $offset);

        Assert::assertEquals(
            count($dataRows),
            count($combinationsList->getCombinations()),
            'Unexpected combinations count'
        );

        $this->assertListedCombinationsProperties($dataRows, $combinationsList->getCombinations());
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
                'Unexpected combination'
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
            Assert::assertEquals(
                $expectedCombination['final price'],
                (string) $editableCombinationForListing->getFinalPrice(),
                'Unexpected combination final price'
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
