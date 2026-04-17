<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Provides data for the product shop selection form, mostly which shops are associated to the product
 * and which one is the current selected shop.
 */
class ProductShopsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var int|null
     */
    private $contextShopId;

    /**
     * @param ProductRepository $productRepository
     * @param int|null $contextShopId
     */
    public function __construct(
        ProductRepository $productRepository,
        ?int $contextShopId
    ) {
        $this->productRepository = $productRepository;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        $associatedShopIds = $this->productRepository->getAssociatedShopIds(new ProductId($id));
        $selectedShops = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $associatedShopIds);

        return [
            'source_shop_id' => $this->contextShopId,
            'initial_shops' => $selectedShops,
            'selected_shops' => $selectedShops,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData()
    {
        // This form is not used for creation only update anyway
        return [];
    }
}
