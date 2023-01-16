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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Context;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
use ProductAssembler;
use ProductPresenterFactory;
use ReflectionException;
use Validate;

class ProductSimplePresenter
{
    /** @var Context */
    private $context;

    /** @var ProductAssembler */
    private $assembler;

    /** @var ProductListingPresenter */
    private $presenter;

    /** @var ProductPresentationSettings */
    private $presentationSettings;

    /** @var self|null */
    private static $instance;

    /** @var ProductLazyArray[]|array  */
    static $presentedProducts = [];

    private function __construct(Context $context = null)
    {
        $this->context = $context ?? Context::getContext();

        $this->assembler = new ProductAssembler($context);
        $presenterFactory = new ProductPresenterFactory($context);
        $this->presentationSettings = $presenterFactory->getPresentationSettings();
        $this->presenter = new ProductListingPresenter(
            new ImageRetriever($context->link),
            $context->link, new PriceFormatter(),
            new ProductColorsRetriever(),
            $context->getTranslator()
        );

    }

    public static function getInstance(Context $context = null): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self($context);
        }

        return self::$instance;
    }

    /**
     * @param Product $product
     * @return ProductLazyArray|null
     * @throws ReflectionException
     */
    public function present(Product $product): ?ProductLazyArray
    {
        if (!Validate::isLoadedObject($product)) {
            return null;
        }

        if (empty(self::$presentedProducts[(int)$product->id])) {
            self::$presentedProducts[$product->id] = $this->presenter->present(
                $this->presentationSettings,
                $this->assembler->assembleProduct(['id_product' => (int)$product->id]),
                $this->context->language
            );
        }

        return self::$presentedProducts[$product->id];
    }

    /**
     * @param Product[] $products
     * @return ProductLazyArray[]|array
     */
    public function presentProducts(array $products): array
    {
        $result = [];

        foreach ($products as $product) {
            if (!$this->isProduct($product)) {
                continue;
            }

            if ($presentedProduct = $this->present($product)) {
                $result[] = $presentedProduct;
            }
        }

        return $result;
    }

    /**
     * @param mixed $product
     * @return bool
     */
    public function isProduct($product): bool
    {
        return $product instanceof Product;
    }
}
