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

namespace PrestaShop\PrestaShop\Adapter\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetAvailableCarriers;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler\GetAvailableCarriersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\FilteredCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\GetCarriersResult;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\ProductSummary;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Product;
use RuntimeException;

#[AsQueryHandler]
class GetAvailableCarriersHandler implements GetAvailableCarriersHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly ProductRepository $productRepository,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function handle(GetAvailableCarriers $query): GetCarriersResult
    {
        $products = [];
        foreach ($query->getProductsIds() as $productId) {
            $product = $this->productRepository->get(new ProductId($productId), new ShopId($this->shopContext->getId()));
            $products[$product->id] = $product;
        }

        $carriersMapping = $this->carrierRepository->findCarriersByProductIds($query->getProductsIds(), new ShopId($this->shopContext->getId()));

        // Compute common carriers across all products
        $commonCarriers = $this->getCommonCarriers($carriersMapping);

        // Index carriers for fast access
        $carriersIndex = $this->indexCarriers($carriersMapping);

        $availableCarriers = [];
        if (!empty($commonCarriers)) {
            foreach ($commonCarriers as $carrierId) {
                $carrier = $carriersIndex[$carrierId];
                $availableCarriers[] = new CarrierSummary($carrier['id_carrier'], $carrier['name']);
            }
        }

        // Compute filtered carriers (carriers not available for all products)
        $removedCarriers = [];
        $allCarrierIds = $this->mapCarrierToProducts($carriersMapping);

        foreach ($allCarrierIds as $carrierId => $productIds) {
            if (!in_array($carrierId, $commonCarriers, true)) {
                $carrier = $carriersIndex[$carrierId];
                $productPreviews = array_map(function (int $pid) use ($products) {
                    $product = $products[$pid];

                    return new ProductSummary($product->id, $this->getProductName($product));
                }, array_keys($productIds));

                $removedCarriers[] = new FilteredCarrier(
                    $productPreviews,
                    new CarrierSummary($carrier['id_carrier'], $carrier['name'])
                );
            }
        }

        return new GetCarriersResult($availableCarriers, $removedCarriers);
    }

    /**
     * Compute intersection of carriers across all products.
     *
     * @return int[]
     */
    private function getCommonCarriers(array $carriersMapping): array
    {
        $commonCarriers = null;

        foreach ($carriersMapping as $carriers) {
            $carrierIds = array_column($carriers, 'id_carrier');

            $commonCarriers = is_null($commonCarriers)
                ? $carrierIds
                : array_intersect($commonCarriers, $carrierIds);
        }

        return $commonCarriers ?? [];
    }

    /**
     * Build a fast-access index of carriers by ID.
     *
     * @return array<int, array{id_carrier: int, name: string}>
     */
    private function indexCarriers(array $carriersMapping): array
    {
        $index = [];
        foreach ($carriersMapping as $carriers) {
            foreach ($carriers as $carrier) {
                $index[$carrier['id_carrier']] = $carrier;
            }
        }

        return $index;
    }

    /**
     * Map carriers to the list of products they are associated with.
     *
     * @return array<int, array<int, true>>
     */
    private function mapCarrierToProducts(array $carriersMapping): array
    {
        $map = [];
        foreach ($carriersMapping as $productId => $carriers) {
            foreach ($carriers as $carrier) {
                $map[$carrier['id_carrier']][$productId] = true;
            }
        }

        return $map;
    }

    /**
     * Retrieve the product name for the current language. Throws exception if not found.
     *
     * @throws RuntimeException
     */
    private function getProductName(Product $product): string
    {
        if (is_array($product->name)) {
            $languageId = $this->languageContext->getId();

            if (!isset($product->name[$languageId])) {
                throw new RuntimeException(sprintf('Product name not found for product ID %d and language ID %d.', $product->id, $languageId));
            }

            return $product->name[$languageId];
        }

        return $product->name;
    }
}
