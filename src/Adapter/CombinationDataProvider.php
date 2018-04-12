<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShop\PrestaShop\Adapter\Tools;
use Tools as ToolsLegacy;
use Product;
use Combination;

/**
 * This class will provide data from DB / ORM about product combination
 */
class CombinationDataProvider
{
    private $context;
    private $productAdapter;
    private $cldrRepository;

    public function __construct()
    {
        $this->context = new LegacyContext();
        $this->productAdapter = new ProductDataProvider();
        $this->cldrRepository = ToolsLegacy::getCldr($this->context->getContext());
    }

    /**
     * Get a combination values
     * @deprecated since 1.7.3.1 really slow, use getFormCombinations instead.
     *
     * @param int $combinationId The id_product_attribute
     *
     * @return array combinations
     */
    public function getFormCombination($combinationId)
    {
        $product = new Product((new Combination($combinationId))->id_product);

        return $this->completeCombination(
            $product->getAttributeCombinationsById(
                $combinationId,
                $this->context->getContext()->language->id
            ),
            $product
        );
    }

    /**
     * Retrieve combinations data for a specific language id.
     * @param array $combinationIds
     * @param int $languageId
     * @return array a list of formatted combinations.
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getFormCombinations(array $combinationIds, int $languageId)
    {
        $productId = (new Combination($combinationIds[0]))->id_product;
        $product = new Product($productId);
        $combinations = array();

        foreach ($combinationIds as $combinationId) {
            $combinations[$combinationId] = $this->completeCombination(
                $product->getAttributeCombinationsById(
                    $combinationId,
                    $languageId
                ),
                $product
            );
        }

        return $combinations;
    }

    /**
     * @param $attributesCombinations
     * @param $product
     * @return array
     */
    public function completeCombination($attributesCombinations, $product)
    {
        $combination = $attributesCombinations[0];

        $attribute_price_impact = 0;
        if ($combination['price'] > 0) {
            $attribute_price_impact = 1;
        } elseif ($combination['price'] < 0) {
            $attribute_price_impact = -1;
        }

        $attribute_weight_impact = 0;
        if ($combination['weight'] > 0) {
            $attribute_weight_impact = 1;
        } elseif ($combination['weight'] < 0) {
            $attribute_weight_impact = -1;
        }

        $attribute_unity_price_impact = 0;
        if ($combination['unit_price_impact'] > 0) {
            $attribute_unity_price_impact = 1;
        } elseif ($combination['unit_price_impact'] < 0) {
            $attribute_unity_price_impact = -1;
        }

        $finalPrice = (new Number((string) $product->price))
            ->plus(new Number((string) $combination['price']))
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);

        return array(
            'id_product_attribute' => $combination['id_product_attribute'],
            'attribute_reference' => $combination['reference'],
            'attribute_ean13' => $combination['ean13'],
            'attribute_isbn' => $combination['isbn'],
            'attribute_upc' => $combination['upc'],
            'attribute_wholesale_price' => $combination['wholesale_price'],
            'attribute_price_impact' => $attribute_price_impact,
            'attribute_price' => $combination['price'],
            'attribute_price_display' => $this->cldrRepository->getPrice($combination['price'], $this->context->getContext()->currency->iso_code),
            'final_price' => (string) $finalPrice,
            'attribute_priceTI' => '',
            'attribute_ecotax' => $combination['ecotax'],
            'attribute_weight_impact' => $attribute_weight_impact,
            'attribute_weight' => $combination['weight'],
            'attribute_unit_impact' => $attribute_unity_price_impact,
            'attribute_unity' => $combination['unit_price_impact'],
            'attribute_minimal_quantity' => $combination['minimal_quantity'],
            'attribute_low_stock_threshold' => $combination['low_stock_threshold'],
            'attribute_low_stock_alert' => (bool) $combination['low_stock_alert'],
            'available_date_attribute' =>  $combination['available_date'],
            'attribute_default' => (bool)$combination['default_on'],
            'attribute_quantity' => $this->productAdapter->getQuantity($product->id, $combination['id_product_attribute']),
            'name' => $this->getCombinationName($attributesCombinations),
            'id_product' => $product->id,
        );
    }

    private function getCombinationName($attributesCombinations)
    {
        $name = array();

        foreach ($attributesCombinations as $attribute) {
            $name[] = $attribute['group_name'].' - '.$attribute['attribute_name'];
        }

        return implode(', ', $name);
    }
}
