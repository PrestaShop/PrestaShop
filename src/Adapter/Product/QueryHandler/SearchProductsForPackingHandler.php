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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Combination;
use Link;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForPacking;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForPackingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForPacking;

/**
 * Handles @see SearchProductsForPacking query
 */
final class SearchProductsForPackingHandler implements SearchProductsForPackingHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var Link
     */
    private $contextLink;

    /**
     * @param ProductRepository $productRepository
     * @param int $contextLangId
     * @param Link $contextLink
     */
    public function __construct(
        ProductRepository $productRepository,
        int $contextLangId,
        Link $contextLink
    ) {
        $this->productRepository = $productRepository;
        $this->contextLangId = $contextLangId;
        $this->contextLink = $contextLink;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SearchProductsForPacking $query): array
    {
        $products = $this->productRepository->searchByNameAndReference(
            $query->getPhrase(),
            $this->contextLangId,
            $query->getLimit()
        );

        return $this->formatProductsForPacking($products);
    }

    /**
     * @param array $products
     *
     * @return ProductForPacking[]
     */
    private function formatProductsForPacking(array $products): array
    {
        $productsForPacking = [];
        $combinationsFeatureIsOn = (bool) Combination::isFeatureActive();

        foreach ($products as $product) {
            if ($combinationsFeatureIsOn && $product['cache_default_attribute']) {
                $combinations = $this->productRepository->getCombinations(
                    (int) $product['id_product'],
                    $this->contextLangId
                );

                if (!empty($combinations)) {
                    $productsForPacking[] = $this->formatCombinationsForPacking($product, $combinations);

                    continue;
                }
            }

            $productsForPacking[] = $this->formatProductForPacking($product);
        }

        return $productsForPacking;
    }

    /**
     * @param array $product
     *
     * @return ProductForPacking
     */
    private function formatProductForPacking(array $product): ProductForPacking
    {
        return new ProductForPacking(
            (int) $product['id_product'],
            $product['name'],
            $product['reference'],
            $this->getImage($product['link_rewrite'], (int) $product['id_image'])
        );
    }

    /**
     * @param array $product
     * @param array $combinations
     *
     * @return ProductForPacking[]
     */
    private function formatCombinationsForPacking(array $product, array $combinations): array
    {
        $combinationsForPacking = [];

        foreach ($combinations as $combination) {
            $combinationsForPacking[] = new ProductForPacking(
                (int) $combination['id_product'],
                $this->buildCombinationName($product, $combination),
                $combination['reference'],
                $this->getImage($product['link_rewrite'], (int) $combination['id_image']),
                (int) $combination['id_product_attribute']
            );
        }

        return $combinationsForPacking;
    }

    /**
     * @param array $product
     * @param array $combination
     *
     * @return string
     */
    private function buildCombinationName(array $product, array $combination): string
    {
        return sprintf(
            '%s %s-%s',
            $product['name'],
            $combination['group_name'],
            $combination['attribute_name']
        );
    }

    /**
     * @param string $linkRewrite
     * @param int $id
     *
     * @return string
     */
    private function getImage(string $linkRewrite, int $id): string
    {
        return $this->contextLink->getImageLink($linkRewrite, $id, 'small_default');
    }
}
