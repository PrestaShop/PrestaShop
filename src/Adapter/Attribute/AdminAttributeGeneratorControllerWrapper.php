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

namespace PrestaShop\PrestaShop\Adapter\Attribute;

use AdminAttributeGeneratorController;
use Combination;
use Context;
use Product;
use SpecificPriceRule;
use Stock;
use StockAvailable;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Admin controller wrapper for new Architecture, about Category admin controller.
 */
class AdminAttributeGeneratorControllerWrapper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct()
    {
        $context = Context::getContext();
        $this->translator = $context->getTranslator();
    }

    /**
     * Generate product attributes.
     *
     * @param object $product The product
     * @param array $options The array with all attributes combinations
     */
    public function processGenerate($product, $options)
    {
        SpecificPriceRule::disableAnyApplication();

        //add combination if not already exists
        $combinations = array_values(AdminAttributeGeneratorController::createCombinations(array_values($options)));
        $combinationsValues = array_values(array_map(function () use ($product) {
            return [
                'id_product' => $product->id,
            ];
        }, $combinations));

        $product->generateMultipleCombinations($combinationsValues, $combinations, false);

        Product::updateDefaultAttribute($product->id);
        SpecificPriceRule::enableAnyApplication();
        SpecificPriceRule::applyAllRules([(int) $product->id]);
    }

    /**
     * Delete a product attribute.
     *
     * @param int $idAttribute The attribute ID
     * @param int $idProduct The product ID
     *
     * @return array
     */
    public function ajaxProcessDeleteProductAttribute($idAttribute, $idProduct)
    {
        if (!Combination::isFeatureActive()) {
            return false;
        }

        if ($idProduct && Validate::isUnsignedId($idProduct) && Validate::isLoadedObject($product = new Product($idProduct))) {
            if (($depends_on_stock = StockAvailable::dependsOnStock($idProduct)) && StockAvailable::getQuantityAvailableByProduct($idProduct, $idAttribute)) {
                return [
                    'status' => 'error',
                    'message' => $this->translator->trans('It is not possible to delete a combination while it still has some quantities in the Advanced Stock Management. You must delete its stock first.', [], 'Admin.Catalog.Notification'),
                ];
            } else {
                $product->deleteAttributeCombination((int) $idAttribute);
                $product->checkDefaultAttributes();
                Tools::clearColorListCache((int) $product->id);
                if (!$product->hasAttributes()) {
                    $product->cache_default_attribute = 0;
                    $product->update();
                } else {
                    Product::updateDefaultAttribute($idProduct);
                }

                if ($depends_on_stock && !Stock::deleteStockByIds($idProduct, $idAttribute)) {
                    return [
                        'status' => 'error',
                        'message' => $this->translator->trans('Error while deleting the stock', [], 'Admin.Catalog.Notification'),
                    ];
                } else {
                    return [
                        'status' => 'ok',
                        'message' => $this->translator->trans('Successful deletion', [], 'Admin.Catalog.Notification'),
                    ];
                }
            }
        } else {
            return [
                'status' => 'error',
                'message' => $this->translator->trans('You cannot delete this attribute.', [], 'Admin.Catalog.Notification'),
            ];
        }
    }
}
