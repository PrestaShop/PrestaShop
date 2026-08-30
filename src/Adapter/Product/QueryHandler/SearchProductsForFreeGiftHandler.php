<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForFreeGift;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForFreeGiftHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForFreeGift;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsQueryHandler]
class SearchProductsForFreeGiftHandler implements SearchProductsForFreeGiftHandlerInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductImagePathFactory $productImagePathFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SearchProductsForFreeGift $query): array
    {
        $foundProducts = $this->productRepository->searchProductsForFreeGift(
            $query->getPhrase(),
            $query->getLanguageId(),
            $query->getShopId(),
            $query->getLimit()
        );

        $productsForFreeGift = [];
        foreach ($foundProducts as $foundProduct) {
            $productsForFreeGift[] = $this->createResult($foundProduct);
        }

        return $productsForFreeGift;
    }

    private function createResult(array $foundProduct): ProductForFreeGift
    {
        if (empty($foundProduct['id_image'])) {
            $imagePath = $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT);
        } else {
            $imagePath = $this->productImagePathFactory->getPathByType(
                new ImageId((int) $foundProduct['id_image']),
                ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT
            );
        }

        [$disabled, $disabledReason] = $this->checkEligibility($foundProduct);

        return new ProductForFreeGift(
            (int) $foundProduct['id_product'],
            $foundProduct['name'],
            $foundProduct['reference'] ?? '',
            $imagePath,
            $foundProduct['product_type'],
            $disabled,
            $disabledReason,
        );
    }

    /**
     * @param array $product
     *
     * @return array{bool, string|null}
     */
    private function checkEligibility(array $product): array
    {
        if (empty($product['available_for_order'])) {
            return [true, $this->translator->trans('This product is not available for order.', [], 'Admin.Catalog.Notification')];
        }

        if ((int) ($product['minimal_quantity'] ?? 0) > 1) {
            return [true, $this->translator->trans('This product requires a minimum quantity greater than 1.', [], 'Admin.Catalog.Notification')];
        }

        if ((int) ($product['customizable'] ?? 0) === ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION) {
            return [true, $this->translator->trans('This product has required customization fields.', [], 'Admin.Catalog.Notification')];
        }

        $stockQuantity = (int) ($product['stock_quantity'] ?? 0);
        $outOfStock = (int) ($product['out_of_stock'] ?? 0);
        // out_of_stock: 0 = deny orders, 1 = allow orders, 2 = use global setting
        if ($outOfStock === 2) {
            $outOfStock = (int) Configuration::get('PS_ORDER_OUT_OF_STOCK');
        }
        if ($stockQuantity <= 0 && $outOfStock === 0) {
            return [true, $this->translator->trans('This product is out of stock.', [], 'Admin.Catalog.Notification')];
        }

        return [false, null];
    }
}
