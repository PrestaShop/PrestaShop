<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tag\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Query\GetTagForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tag\QueryHandler\GetTagForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\QueryResult\EditableTag;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use Tag;

#[AsQueryHandler]
class GetTagForEditingHandler implements GetTagForEditingHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @param ProductRepository $productRepository
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImagePathFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
    }

    public function handle(GetTagForEditing $query): EditableTag
    {
        $tag = $this->getLegacyTagObject($query->getTagId());

        $products = [];
        foreach ($tag->getProducts() as $product) {
            $products[] = $this->getTagProduct($product);
        }

        return new EditableTag(
            $tag->name,
            $tag->id_lang,
            $products
        );
    }

    /**
     * @param TagId $tagId
     *
     * @return Tag
     */
    protected function getLegacyTagObject(TagId $tagId): Tag
    {
        $tag = new Tag($tagId->getValue());

        if ($tag->id !== $tagId->getValue()) {
            throw new TagNotFoundException(
                sprintf('Tag with id "%d" was not found', $tagId->getValue())
            );
        }

        return $tag;
    }

    /**
     * @return array{id: int, name: string, image: string}
     */
    protected function getTagProduct(array $product): array
    {
        $productId = new ProductId((int) $product['id_product']);
        $shopId = $this->productRepository->getProductDefaultShopId($productId);
        $imageId = $this->productImageRepository->getDefaultImageId($productId, $shopId);
        $imagePath = $imageId ?
            $this->productImagePathFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT) :
            $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT)
        ;

        return [
            'id' => (int) $product['id_product'],
            'name' => $product['name'],
            'image' => $imagePath,
        ];
    }
}
