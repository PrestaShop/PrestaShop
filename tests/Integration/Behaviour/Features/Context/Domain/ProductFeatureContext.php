<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Cache;
use Context;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then I set tax rule group :taxRulesGroupReference to product :productReference
     *
     * @param string $taxRulesGroupReference
     * @param string $productName
     */
    public function setProductTaxRulesGroup(string $taxRulesGroupReference, string $productName)
    {
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);
        $productId = $this->getProductIdByName($productName);

        $product = new Product($productId);
        $product->id_tax_rules_group = $taxRulesGroupId;
        $product->save();

        // Important to clean this cache or Product::getIdTaxRulesGroupByIdProduct still returns the initial value
        Cache::clean('product_id_tax_rules_group_*');
    }

    /**
     * @When I generate attribute combinations for product :productName with following values in default language:
     *
     * @param string $productName
     * @param TableNode $table
     */
    public function generateCombinations(string $productName, TableNode $table)
    {
        $productId = $this->getProductIdByName($productName);

        $this->getCommandBus()->handle(new GenerateCombinationsCommand($productId, $this->getAttributesByGroupsFromInputTable($table)));
        //@todo; try catch for errors.
    }

    //@todo; check if correct combinations were generated and added to db;

    /**
     * @param TableNode $table
     *
     * @return array
     */
    private function getAttributesByGroupsFromInputTable(TableNode $table): array
    {
        $attributeNamesByGroupName = $table->getRowsHash();
        $attributesByGroups = [];

        foreach ($attributeNamesByGroupName as $groupName => $attributeNames) {
            $attributeNames = PrimitiveUtils::castStringArrayIntoArray($attributeNames);
            $groupAttributes = [];

            foreach ($attributeNames as $attributeName) {
                $groupAttributes[] = $this->getAttributeIdByName($attributeName);
            }

            $attributesByGroups[$this->getAttributeGroupIdByName($groupName)] = $groupAttributes;
        }

        return $attributesByGroups;
    }

    /**
     * @param string $name
     *
     * @return int
     * @todo: any better way to maintain behat principles of feature readability? cant find a way to search for attribute
     */
    private function getAttributeIdByName(string $name): int
    {
        $attributeNameByIdMap = [
            'S' => 1,
            'M' => 2,
            'L' => 3,
            'Red' => 10,
            '40x60' => 19,
        ];

        if (array_key_exists($name, $attributeNameByIdMap)) {
            return $attributeNameByIdMap[$name];
        }

        throw new RuntimeException(sprintf(
            'Attribute id by name %s was not found in values map. Map it manually from fixtures value',
            $name
        ));
    }

    /**
     * @param string $name
     *
     * @return string
     * @todo: any better way to maintain behat principles of feature readability? cant find a way to search for attribute
     */
    private function getAttributeGroupIdByName(string $name): string
    {
        $attributeGroupIdNameMap = [
            'Size' => 1,
            'Color' => 2,
            'Dimension' => 3,
        ];

        if (array_key_exists($name, $attributeGroupIdNameMap)) {
            return $attributeGroupIdNameMap[$name];
        }

        throw new RuntimeException(sprintf(
            'Attribute group id by name %s was not found in values map. Map it manually from fixtures value',
            $name
        ));
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductIdByName(string $productName): int
    {
        /** @var FoundProduct[] */
        $products = $this->getQueryBus()->handle(new SearchProducts($productName, 1, Context::getContext()->currency->iso_code));

        if (empty($products)) {
            throw new RuntimeException(sprintf('Product with name "%s" was not found', $productName));
        }

        /** @var FoundProduct $product */
        $product = reset($products);

        return $product->getProductId();
    }
}
