<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Provides the data that is used to prefill the Product image form
 */
class ProductImageFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $contextShopId;

    public function __construct(
        CommandBusInterface $queryBus,
        int $contextShopId
    ) {
        $this->queryBus = $queryBus;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        /** @var ProductImage $productImage */
        $productImage = $this->queryBus->handle(new GetProductImage(
            (int) $id,
            ShopConstraint::shop($this->contextShopId)
        ));

        return [
            'legend' => $productImage->getLocalizedLegends(),
            'is_cover' => $productImage->isCover(),
            'position' => $productImage->getPosition(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData()
    {
        return [];
    }
}
