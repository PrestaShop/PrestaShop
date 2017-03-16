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
namespace PrestaShop\PrestaShop\Adapter\Product;

/**
 * This class will provide data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class ProductDataProvider
{
    /**
     * Get a new ProductCore instance
     *
     * @return object product
     */
    public function getProductInstance()
    {
        return new \ProductCore();
    }

    /**
     * Get a product
     *
     * @param int $id_product
     * @param bool $full
     * @param int|null $id_lang
     * @param int|null $id_shop
     * @param object|null $context
     *
     * @throws \LogicException If the product id is not set
     *
     * @return \ProductCore $product
     */
    public function getProduct($id_product, $full = false, $id_lang = null, $id_shop = null, $context = null)
    {
        if (!$id_product) {
            throw new \LogicException('You need to provide a product id', 5002);
        }

        $product = new \ProductCore($id_product, $full, $id_lang, $id_shop, $context);
        if ($product) {
            if (!is_array($product->link_rewrite)) {
                $linkRewrite = $product->link_rewrite;
            } else {
                $linkRewrite = $product->link_rewrite[$id_lang ? $id_lang : key($product->link_rewrite)];
            }

            $cover = \ProductCore::getCover($product->id);
            $product->image = \Context::getContext()->link->getImageLink($linkRewrite, $cover ? $cover['id_image'] : '', 'home_default');
        }

        return $product;
    }

    /**
     * Get default taxe rate product
     *
     * @return int id tax rule group
     */
    public function getIdTaxRulesGroup()
    {
        $product = new \ProductCore();
        return $product->getIdTaxRulesGroup();
    }

    /**
     * Get product quantity
     *
     * @param int $id_product
     * @param int|null $id_product_attribute
     * @param bool|null $cache_is_pack
     *
     * @return int stock
     */
    public function getQuantity($id_product, $id_product_attribute = null, $cache_is_pack = null)
    {
        return \ProductCore::getQuantity($id_product, $id_product_attribute, $cache_is_pack);
    }

    /**
     * Get associated images to product
     *
     * @param int $id_product
     * @param int $id_lang
     *
     * @return array
     */
    public function getImages($id_product, $id_lang)
    {
        $data = [];
        foreach (\ImageCore::getImages($id_lang, $id_product) as $image) {
            $data[] = $this->getImage($image['id_image']);
        }

        return $data;
    }

    /**
     * Get an image
     *
     * @param int $id_image
     *
     * @return object
     */
    public function getImage($id_image)
    {
        $imageData = new \ImageCore((int)$id_image);

        return [
            'id' => $imageData->id,
            'id_product' => $imageData->id_product,
            'position' => $imageData->position,
            'cover' => $imageData->cover ? true : false,
            'legend' => $imageData->legend,
            'format' => $imageData->image_format,
            'base_image_url' => _THEME_PROD_DIR_.$imageData->getImgPath(),
        ];
    }
}
