<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

/**
 * Admin controller wrapper for new Architecture, about Product admin controller.
 */
class AdminProductWrapper
{
    /**
     * getInstance
     * Get the legacy AdminProductsControllerCore instance
     *
     * @return \AdminProductsControllerCore instance
     */
    public function getInstance()
    {
        return new \AdminProductsControllerCore();
    }

    /**
     * processProductAttribute
     * Update a combination
     *
     * @param object $product
     * @param array $combinationValues the posted values
     *
     * @return \AdminProductsControllerCore instance
     */
    public function processProductAttribute($product, $combinationValues)
    {
        $id_product_attribute = (int)$combinationValues['id_product_attribute'];

        if (!\Combination::isFeatureActive() || $id_product_attribute == 0) {
            return;
        }

        if (!isset($combinationValues['attribute_wholesale_price'])) {
            $combinationValues['attribute_wholesale_price'] = 0;
        }
        if (!isset($combinationValues['attribute_price_impact'])) {
            $combinationValues['attribute_price_impact'] = 0;
        }
        if (!isset($combinationValues['attribute_weight_impact'])) {
            $combinationValues['attribute_weight_impact'] = 0;
        }
        if (!isset($combinationValues['attribute_ecotax'])) {
            $combinationValues['attribute_ecotax'] = 0;
        }
        if ($combinationValues['attribute_default']) {
            $product->deleteDefaultAttributes();
        }

        $product->updateAttribute(
            $id_product_attribute,
            $combinationValues['attribute_wholesale_price'],
            $combinationValues['attribute_price'] * $combinationValues['attribute_price_impact'],
            $combinationValues['attribute_weight'] * $combinationValues['attribute_weight_impact'],
            $combinationValues['attribute_unity'] * $combinationValues['attribute_unit_impact'],
            $combinationValues['attribute_ecotax'],
            null,
            $combinationValues['attribute_reference'],
            $combinationValues['attribute_ean13'],
            $combinationValues['attribute_default'],
            isset($combinationValues['attribute_location']) ? $combinationValues['attribute_location'] : null,
            $combinationValues['attribute_upc'],
            $combinationValues['attribute_minimal_quantity'],
            $combinationValues['available_date_attribute'],
            false,
            array(),
            $combinationValues['attribute_isbn']
        );

        \StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, $id_product_attribute);
        \StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, $id_product_attribute);

        $product->checkDefaultAttributes();

        if ($combinationValues['attribute_default']) {
            \Product::updateDefaultAttribute((int)$product->id);
            if (isset($id_product_attribute)) {
                $product->cache_default_attribute = (int)$id_product_attribute;
            }

            if ($available_date = $combinationValues['available_date_attribute']) {
                $product->setAvailableDate($available_date);
            } else {
                $product->setAvailableDate();
            }
        }

        $this->processQuantityUpdate($product, $combinationValues['attribute_quantity'], $id_product_attribute);
    }

    /**
     * Update a quantity for a product or a combination.
     *
     * Does not work in Advanced stock management.
     *
     * @param \Product $product
     * @param integer $quantity
     * @param integer $forAttributeId
     */
    public function processQuantityUpdate(\Product $product, $quantity, $forAttributeId = 0)
    {
        // Hook triggered by legacy code below: actionUpdateQuantity('id_product', 'id_product_attribute', 'quantity')
        \StockAvailable::setQuantity((int)$product->id, $forAttributeId, $quantity);
        \Hook::exec('actionProductUpdate', array('id_product' => (int)$product->id, 'product' => $product));
    }

    /**
     * Set if a product depends on stock (ASM). For a product or a combination.
     *
     * Does work only in Advanced stock management.
     *
     * @param \Product $product
     * @param boolean $dependsOnStock
     * @param integer $forAttributeId
     */
    public function processDependsOnStock(\Product $product, $dependsOnStock, $forAttributeId = 0)
    {
        \StockAvailable::setProductDependsOnStock((int)$product->id, $dependsOnStock, null, $forAttributeId);
    }
}
