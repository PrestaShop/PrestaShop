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

use Link;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForRedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForRedirectOptionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForRedirectOption;

/**
 * Handles @see SearchProductsForRedirectOption query
 */
final class SearchProductsForRedirectOptionHandler implements SearchProductsForRedirectOptionHandlerInterface
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
    public function handle(SearchProductsForRedirectOption $query): array
    {
        $results = $this->productRepository->searchByNameAndReference(
            $query->getPhrase(),
            $this->contextLangId,
            $query->getLimit()
        );

        $productsToRelate = [];
        foreach ($results as $result) {
            $productsToRelate[] = new ProductForRedirectOption(
                (int) $result['id_product'],
                $result['name'],
                $result['reference'],
                $this->contextLink->getImageLink('product', $result['id_image'], 'small_default')
            );
        }

        return $productsToRelate;
    }
}
