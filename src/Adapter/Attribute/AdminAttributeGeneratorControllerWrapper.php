<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute;

use Context;

/**
 * Admin controller wrapper for new Architecture, about Category admin controller.
 */
class AdminAttributeGeneratorControllerWrapper
{
    private $translator;

    public function __construct()
    {
        $context = Context::getContext();
        $this->translator = $context->getTranslator();
    }

    /**
     * Generate product attributes
     *
     * @param object $product The product
     * @param array $options The array with all attributes combinations
     */
    public function processGenerate($product, $options)
    {
        \SpecificPriceRuleCore::disableAnyApplication();

        //add combination if not already exists
        $combinations = array_values(\AdminAttributeGeneratorControllerCore::createCombinations(array_values($options)));
        $combinationsValues = array_values(array_map(function () use ($product) {
            return array(
                'id_product' => $product->id
            );
        }, $combinations));

        $product->generateMultipleCombinations($combinationsValues, $combinations, false);

        \ProductCore::updateDefaultAttribute($product->id);
        \SpecificPriceRuleCore::enableAnyApplication();
        \SpecificPriceRuleCore::applyAllRules(array((int)$product->id));
    }

    /**
     * Delete a product attribute
     *
     * @param int $idAttribute The attribute ID
     * @param int $idProduct The product ID
     *
     * @return array
     */
    public function ajaxProcessDeleteProductAttribute($idAttribute, $idProduct)
    {
        if (!\CombinationCore::isFeatureActive()) {
            return false;
        }

        if ($idProduct && \ValidateCore::isUnsignedId($idProduct) && \ValidateCore::isLoadedObject($product = new \ProductCore($idProduct))) {
            if (($depends_on_stock = \StockAvailableCore::dependsOnStock($idProduct)) && \StockAvailableCore::getQuantityAvailableByProduct($idProduct, $idAttribute)) {
                return array(
                    'status' => 'error',
                    'message'=> $this->translator->trans('It is not possible to delete a combination while it still has some quantities in the Advanced Stock Management. You must delete its stock first.', array(), 'Admin.Catalog.Notification'),
                );
            } else {
                $product->deleteAttributeCombination((int)$idAttribute);
                $product->checkDefaultAttributes();
                \ToolsCore::clearColorListCache((int)$product->id);
                if (!$product->hasAttributes()) {
                    $product->cache_default_attribute = 0;
                    $product->update();
                } else {
                    \ProductCore::updateDefaultAttribute($idProduct);
                }

                if ($depends_on_stock && !\StockCore::deleteStockByIds($idProduct, $idAttribute)) {
                    return array(
                        'status' => 'error',
                        'message'=> $this->translator->trans('Error while deleting the stock', array(), 'Admin.Catalog.Notification'),
                    );
                } else {
                    return array(
                        'status' => 'ok',
                        'message'=> $this->translator->trans('Successful deletion', array(), 'Admin.Catalog.Notification'),
                    );
                }
            }
        } else {
            return array(
                'status' => 'error',
                'message'=> $this->translator->trans('You cannot delete this attribute.', array(), 'Admin.Catalog.Notification'),
            );
        }
    }
}
