<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Configuration;
use Context;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Product;
use ProductPresenterFactory;
use Tax;
use TaxConfiguration;

final class ProductPageProductPreparation implements ProductPageProductPreparationInterface
{
    public function __construct(
        private readonly ProductQuantityDiscountProvider $quantityDiscountProvider,
    ) {
    }

    public function presentObject(Product $product): array
    {
        return (new ObjectPresenter())->present($product);
    }

    public function transformDescriptionWithImg(
        string $description,
        Product $product,
        Context $context,
    ): string {
        $pattern = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($pattern, $description, $matches)) {
            $imageLink = $context->link->getImageLink($product->link_rewrite, $matches[1], $matches[3]);
            $class = $matches[2] === 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $description = str_replace($matches[0], '<img src="' . $imageLink . '" alt="" ' . $class . '/>', $description);
        }

        return $description;
    }

    public function getProductMinimalQuantity(
        ProductLazyArray|array $productForPresentation,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        ?callable $combinationResolver = null,
    ): int {
        $minimalQuantity = 1;
        if ($idProductAttribute) {
            $combination = $combinationResolver
                ? $combinationResolver($idProductAttribute)
                : $this->findProductCombinationById($product, $context, $idProductAttribute);
            if ($combination && $combination['minimal_quantity']) {
                $minimalQuantity = (int) $combination['minimal_quantity'];
            }
        } else {
            $minimalQuantity = (int) $product->minimal_quantity;
        }

        return max(1, (int) $minimalQuantity);
    }

    public function getRequiredQuantity(
        ProductLazyArray|array $product,
        ?int $minimalQuantity = null,
    ): int {
        return max(1, ($minimalQuantity ?? (int) $product['minimal_quantity']) - (int) $product['cart_quantity']);
    }

    public function getWantedQuantity(
        int $quantityWanted,
        ProductLazyArray|array $product,
        ?int $requiredQuantity = null,
    ): int {
        return max($quantityWanted, $requiredQuantity ?? $this->getRequiredQuantity($product));
    }

    public function findProductCombinationById(
        Product $product,
        Context $context,
        int $combinationId,
    ): ?array {
        $combinations = $product->getAttributesGroups($context->language->id, $combinationId);

        return is_array($combinations) && $combinations ? reset($combinations) : null;
    }

    public function getProductEcotax(
        array $product,
        Product $productObject,
        Context $context,
        ?callable $combinationResolver = null,
    ): float {
        $ecotax = $product['ecotax'];
        if ($product['id_product_attribute']) {
            $combination = $combinationResolver
                ? $combinationResolver((int) $product['id_product_attribute'])
                : $this->findProductCombinationById($productObject, $context, (int) $product['id_product_attribute']);
            if (isset($combination['ecotax']) && $combination['ecotax'] > 0) {
                $ecotax = $combination['ecotax'];
            }
        }
        if ($ecotax) {
            $priceDisplay = $context->smarty->getTemplateVars('priceDisplay');
            $priceDisplay ??= Product::getTaxCalculationMethod((int) $context->cookie->id_customer);
            if ($priceDisplay == 0) {
                $ecotax *= 1 + Tax::getProductEcotaxRate() / 100;
            }
        }

        return (float) $ecotax;
    }

    public function getQuantityLabel(
        int $quantity,
        Context $context,
    ): string {
        return $quantity > 1
            ? $context->getTranslator()->trans('Items', [], 'Shop.Theme.Catalog')
            : $context->getTranslator()->trans('Item', [], 'Shop.Theme.Catalog');
    }

    public function getQuantityDiscounts(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): array {
        return $this->quantityDiscountProvider->getQuantityDiscounts($product, $context, $idProductAttribute);
    }

    public function appendAttributesToTitle(
        string $title,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): string {
        if (!Configuration::get('PS_PRODUCT_ATTRIBUTES_IN_TITLE') || !$idProductAttribute) {
            return $title;
        }

        $attributes = $product->getAttributeCombinationsById($idProductAttribute, $context->language->id);
        if (is_array($attributes) && count($attributes) > 0) {
            foreach ($attributes as $attribute) {
                $title .= ' ' . $attribute['group_name'] . ' ' . $attribute['attribute_name'];
            }
        }

        return $title;
    }

    public function presentProduct(
        array $product,
        Context $context,
    ): ProductLazyArray {
        $factory = new ProductPresenterFactory($context, new TaxConfiguration());

        $presenter = new ProductPresenter(
            new ImageRetriever($context->link),
            $context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $context->getTranslator(),
            new HookManager(),
            new AdapterConfiguration()
        );

        return $presenter->present(
            $factory->getPresentationSettings(),
            $product,
            $context->language
        );
    }

    public function addProductCustomizationData(
        array $productFull,
        Product $product,
        Context $context,
    ): array {
        if (!$productFull['customizable']) {
            $productFull['customizations'] = ['fields' => []];
            $productFull['id_customization'] = 0;
            $productFull['is_customizable'] = false;

            return $productFull;
        }

        $customizationData = ['fields' => []];
        $customizedData = [];
        $idCustomization = 0;
        foreach ($context->cart->getProductCustomization($productFull['id_product'], null, true) as $customization) {
            $idCustomization = $customization['id_customization'];
            $customizedData[$customization['index']] = $customization;
        }

        $customizationFields = $product->getCustomizationFields($context->language->id);
        if (!is_array($customizationFields)) {
            $customizationFields = [];
        }

        foreach ($customizationFields as $customizationField) {
            $key = $customizationField['id_customization_field'];
            $field['label'] = $customizationField['name'];
            $field['id_customization_field'] = $key;
            $field['required'] = $customizationField['required'];
            switch ($customizationField['type']) {
                case Product::CUSTOMIZE_FILE:
                    $field['type'] = 'image';
                    $field['image'] = null;
                    $field['input_name'] = 'file' . $key;
                    break;
                case Product::CUSTOMIZE_TEXTFIELD:
                    $field['type'] = 'text';
                    $field['text'] = '';
                    $field['input_name'] = 'textField' . $key;
                    break;
                default:
                    $field['type'] = null;
            }
            if (array_key_exists($key, $customizedData)) {
                $data = $customizedData[$key];
                $field['is_customized'] = true;
                switch ($customizationField['type']) {
                    case Product::CUSTOMIZE_FILE:
                        $field['image'] = (new ImageRetriever($context->link))->getCustomizationImage($data['value']);
                        $field['remove_image_url'] = $context->link->getProductDeletePictureLink($productFull, $key);
                        break;
                    case Product::CUSTOMIZE_TEXTFIELD:
                        $field['text'] = $data['value'];
                        break;
                }
            } else {
                $field['is_customized'] = false;
            }
            $customizationData['fields'][] = $field;
        }
        $productFull['customizations'] = $customizationData;
        $productFull['id_customization'] = $idCustomization;
        $productFull['is_customizable'] = true;

        return $productFull;
    }
}
