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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetProductCombinationsForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ProductCombinationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I generate combinations for product :productReference using following attributes:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function generateCombinations(string $productReference, TableNode $table): void
    {
        $tableData = $table->getRowsHash();
        $groupedAttributeIds = $this->parseGroupedAttributeIds($tableData);

        try {
            $this->getCommandBus()->handle(new GenerateProductCombinationsCommand(
                $this->getSharedStorage()->get($productReference),
                $groupedAttributeIds
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following combinations:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductCombinations(string $productReference, TableNode $table): void
    {
        $dataRows = $table->getColumnsHash();
        $totalExpectedCombinations = count($dataRows);
        $combinationsForEditing = $this->getQueryBus()->handle(new GetProductCombinationsForEditing(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultLangId(),
            $totalExpectedCombinations
        ));

        Assert::assertEquals(count($combinationsForEditing), $totalExpectedCombinations, 'Unexpected combinations count');

        /** @var ProductCombinationForEditing $combinationForEditing */
        foreach ($combinationsForEditing as $key => $combinationForEditing) {
            Assert::assertEquals(
                $dataRows[$key]['combination name'],
                $combinationForEditing->getCombinationName(),
                'Unexpected combination'
            );
        }
    }

    /**
     * @param string $groupsAttributesReferencesPairs
     *
     * @return array
     */
    private function parseAttributes(string $groupsAttributesReferencesPairs): array
    {
        $attributeByGroupIds = [];
        $groupAttributeReferencesPairs = explode(';', $groupsAttributesReferencesPairs);
        foreach ($groupAttributeReferencesPairs as $groupAttributeReferencesPair) {
            $groupAttributePair = explode(':', $groupAttributeReferencesPair);
            $groupId = $this->getSharedStorage()->get($groupAttributePair[0]);
            $attributeId = $this->getSharedStorage()->get($groupAttributePair[1]);

            $attributeByGroupIds[][$groupId] = $attributeId;
        }

        return $attributeByGroupIds;
    }

    /**
     * @param array $tableData
     *
     * @return array
     */
    private function parseGroupedAttributeIds(array $tableData): array
    {
        $groupedAttributeIds = [];
        foreach ($tableData as $attributeGroupReference => $attributeReferences) {
            $attributeIds = [];
            foreach (PrimitiveUtils::castStringArrayIntoArray($attributeReferences) as $attributeReference) {
                $attributeIds[] = $this->getSharedStorage()->get($attributeReference);
            }

            $groupedAttributeIds[$this->getSharedStorage()->get($attributeGroupReference)] = $attributeIds;
        }

        return $groupedAttributeIds;
    }
}
