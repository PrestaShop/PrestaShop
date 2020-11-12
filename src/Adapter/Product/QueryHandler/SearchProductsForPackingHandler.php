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
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForPacking;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForPackingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForPacking;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

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
     * @var Link
     */
    private $contextLink;

    /**
     * @param ProductRepository $productRepository
     * @param Link $contextLink
     */
    public function __construct(
        ProductRepository $productRepository,
        Link $contextLink
    ) {
        $this->productRepository = $productRepository;
        $this->contextLink = $contextLink;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SearchProductsForPacking $query): array
    {
        //@todo: pack products should not be found
        $products = $this->productRepository->searchByNameAndReference(
            $query->getPhrase(),
            $query->getLanguageId(),
            $query->getLimit()
        );

        return $this->formatProductsForPacking($products, $query->getLanguageId(), $query->getLimit());
    }

    /**
     * @param array $products
     * @param LanguageId $languageId
     * @param int $limit
     *
     * @return ProductForPacking[]
     */
    private function formatProductsForPacking(array $products, LanguageId $languageId, int $limit): array
    {
        $productsForPacking = [];
        $combinationFeatureOn = Combination::isFeatureActive();

        foreach ($products as $product) {
            if ($combinationFeatureOn && $product['cache_default_attribute']) {
                $combinations = $this->productRepository->getCombinations(
                    new ProductId((int) $product['id_product']),
                    $languageId,
                    $limit
                );

                if (!empty($combinations)) {
                    foreach ($combinations as $combination) {
                        if (count($productsForPacking) === $limit) {
                            break;
                        }
                        $productsForPacking[] = $this->formatCombinationForPacking($product, $combination);
                    }

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
     * @param array<string, mixed> $product
     * @param array<string, mixed> $combination
     *
     * @return ProductForPacking
     */
    private function formatCombinationForPacking(array $product, array $combination): ProductForPacking
    {
        return new ProductForPacking(
            (int) $product['id_product'],
            $this->buildCombinationName($product, $combination),
            $combination['reference'],
            $this->getImage($product['link_rewrite'], $combination['id_image']),
            $combination['id_product_attribute']
        );
    }

    /**
     * @param array $product
     * @param array $combination
     *
     * @return string
     */
    private function buildCombinationName(array $product, array $combination): string
    {
        //@todo: PR #20518 has DTO CombinationAttributeInformation & almost similar method for command name building
        //todo  check it if its possible to reuse those
        // it is used in separate commands, so might be worth to extract to some common service with command-independent DTO)
        $combinedNameParts = [];
        foreach ($combination['attributes'] as $attributeInformation) {
            $combinedNameParts[] = sprintf(
                '%s - %s',
                $attributeInformation['group_name'],
                $attributeInformation['attribute_name']
            );
        }

        $attributeNames = implode(', ', $combinedNameParts);

        return sprintf('%s %s', $product['name'], $attributeNames);
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
