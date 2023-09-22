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

use Combination;
use Context;
use PrestaShopBundle\Translation\TranslatorComponent;
use Product;
use SpecificPriceRule;
use Validate;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * Admin controller wrapper for new Architecture, about Category admin controller.
 */
class AdminAttributeGeneratorControllerWrapper
{
    /**
     * @var TranslatorComponent
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
        $combinations = array_values($this->createCombinations(array_values($options)));
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

    public function createCombinations(array $list): array
    {
        if (count($list) <= 1) {
            return count($list) ? array_map(function ($v) {
                return [$v];
            }, $list[0]) : $list;
        }
        $res = [];
        $first = array_pop($list);
        foreach ($first as $attribute) {
            $tab = $this->createCombinations($list);
            foreach ($tab as $to_add) {
                $res[] = is_array($to_add) ? array_merge($to_add, [$attribute]) : [$to_add, $attribute];
            }
        }

        return $res;
    }

    /**
     * Delete a product attribute.
     *
     * @param int $idAttribute The attribute ID
     * @param int $idProduct The product ID
     *
     * @return array|bool
     */
    public function ajaxProcessDeleteProductAttribute($idAttribute, $idProduct)
    {
        if (!Combination::isFeatureActive()) {
            return false;
        }

        if ($idProduct && Validate::isUnsignedId($idProduct) && Validate::isLoadedObject($product = new Product($idProduct))) {
            $product->deleteAttributeCombination((int) $idAttribute);
            $product->checkDefaultAttributes();
            if (!$product->hasAttributes()) {
                $product->cache_default_attribute = 0;
                $product->update();
            } else {
                Product::updateDefaultAttribute($idProduct);
            }

            return [
                'status' => 'ok',
                'message' => $this->translator->trans('Successful deletion', [], 'Admin.Catalog.Notification'),
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $this->translator->trans('You cannot delete this attribute.', [], 'Admin.Catalog.Notification'),
            ];
        }
    }
}
