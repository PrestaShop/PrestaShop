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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
    public function generateCombinationsForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->generateCombinations($productReference, $table, $this->getDefaultShopId());
    }

    /**
     * @When I generate combinations in shop ":shopReference" for product :productReference using following attributes:
     *
     * @param string $shopReference
     * @param string $productReference
     * @param TableNode $table
     */
    public function generateCombinationsForShop(string $shopReference, string $productReference, TableNode $table): void
    {
        $this->generateCombinations(
            $productReference,
            $table,
            $this->getSharedStorage()->get($shopReference)
        );
    }

    /**
     * @Then combination :combinationReference should be named :combinationName
     *
     * @param string $combinationReference
     * @param string $combinationName
     */
    public function assertCombinationName(string $combinationReference, string $combinationName): void
    {
        $combinationForEditing = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId());

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
     * This step checks that the general product_attribute table is empty
     *
     * @Given product ":productReference" has no combinations generated at all
     *
     * @param string $productReference
     */
    public function assertProductHasNoCombinations(string $productReference): void
    {
        /** @var ProductMultiShopRepository $productRepository */
        $productRepository = $this->getContainer()->get('prestashop.adapter.product.repository.product_multi_shop_repository');
        $productId = new ProductId($this->getSharedStorage()->get($productReference));

        Assert::assertFalse($productRepository->hasCombinations($productId));
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

    /**
     * @param string $productReference
     * @param TableNode $table
     * @param int $shopId
     */
    private function generateCombinations(string $productReference, TableNode $table, int $shopId): void
    {
        $tableData = $table->getRowsHash();
        $groupedAttributeIds = $this->parseGroupedAttributeIds($tableData);

        try {
            $this->getCommandBus()->handle(new GenerateProductCombinationsCommand(
                $this->getSharedStorage()->get($productReference),
                $groupedAttributeIds,
                //@todo: not yet handled for all shops
                ShopConstraint::shop($shopId)
            ));
        } catch (InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }
}
