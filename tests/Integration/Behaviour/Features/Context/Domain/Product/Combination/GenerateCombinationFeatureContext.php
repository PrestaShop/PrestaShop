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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use Product;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class GenerateCombinationFeatureContext extends AbstractCombinationFeatureContext
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

        $this->cleanLastException();
        try {
            $this->getCommandBus()->handle(new GenerateProductCombinationsCommand(
                $this->getSharedStorage()->get($productReference),
                $groupedAttributeIds
            ));
        } catch (InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then combination :combinationReference should be named :combinationName
     *
     * @param string $combinationReference
     * @param string $combinationName
     */
    public function assertCombinationName(string $combinationReference, string $combinationName): void
    {
        $combinationForEditing = $this->getCombinationForEditing($combinationReference);

        Assert::assertSame(
            $combinationName,
            $combinationForEditing->getName(),
            sprintf(
                'Unexpected name %s, expected %s',
                $combinationForEditing->getName(),
                $combinationName
            )
        );
    }

    /**
     * @Then product :productReference default combination should be :combinationReference
     *
     * @param string $productReference
     * @param string $combinationReference
     */
    public function assertCachedDefaultCombination(string $productReference, string $combinationReference): void
    {
        $this->assertCachedDefaultCombinationId(
            $productReference,
            $this->getSharedStorage()->get($combinationReference)
        );
    }

    /**
     * @Given product :productReference should not have a default combination
     * @Given product :productReference does not have a default combination
     *
     * @param string $productReference
     */
    public function assertProductHasNoCachedDefaultCombination(string $productReference): void
    {
        $this->assertCachedDefaultCombinationId($productReference, 0);
    }

    /**
     * @param string $productReference
     * @param int $combinationId
     */
    private function assertCachedDefaultCombinationId(string $productReference, int $combinationId): void
    {
        $product = new Product($this->getSharedStorage()->get($productReference));

        Assert::assertEquals(
            (int) $product->cache_default_attribute,
            $combinationId,
            'Unexpected cached product default combination'
        );
    }

    /**
     * @param array $groupedReferences
     *
     * @return array
     */
    private function parseGroupedAttributeIds(array $groupedReferences): array
    {
        $groupedAttributeIds = [];
        foreach ($groupedReferences as $attributeGroupReference => $attributeReferences) {
            $attributeIds = [];
            foreach (PrimitiveUtils::castStringArrayIntoArray($attributeReferences) as $attributeReference) {
                $attributeIds[] = $this->getSharedStorage()->get($attributeReference);
            }

            $groupedAttributeIds[$this->getSharedStorage()->get($attributeGroupReference)] = $attributeIds;
        }

        return $groupedAttributeIds;
    }
}
