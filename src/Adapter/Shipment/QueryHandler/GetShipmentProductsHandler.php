<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use OrderDetail;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipmentProduct;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Product;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class GetShipmentProductsHandler implements GetShipmentProductsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
        private readonly LanguageContext $languageContext,
        private readonly ProductImagePathFactory $productImageUrlFactory,
        private readonly ProductRepository $productRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GetShipmentProducts $query
     *
     * @return OrderShipmentProduct[]
     */
    public function handle(GetShipmentProducts $query)
    {
        $shipmentProducts = [];
        $shipmentId = $query->getShipmentId()->getValue();

        try {
            $result = $this->repository->findOneBy(['id' => $shipmentId]);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }

        if (!empty($result)) {
            foreach ($result->getProducts() as $product) {
                $orderDetail = new OrderDetail($product->getOrderDetailId());
                $productInstance = $this->productRepository->get(
                    new ProductId($orderDetail->product_id),
                    new ShopId($orderDetail->id_shop)
                );

                $image = $productInstance->getCover($orderDetail->product_id);
                $imagePath = $image
                    ? $this->productImageUrlFactory->getPathByType(new ImageId($image['id_image']), ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT)
                    : $this->productImageUrlFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT);

                $productName = $this->getProductName($productInstance);
                $productReference = $productInstance->reference;

                $shipmentProducts[] = new OrderShipmentProduct(
                    $product->getOrderDetailId(),
                    $product->getQuantity(),
                    $productName,
                    $productReference,
                    $imagePath
                );
            }
        }

        return $shipmentProducts;
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
                throw new RuntimeException(
                    sprintf('Product name not found for product ID %d and language ID %d.', $product->id, $languageId)
                );
            }

            return $product->name[$languageId];
        }

        return $product->name;
    }
}
