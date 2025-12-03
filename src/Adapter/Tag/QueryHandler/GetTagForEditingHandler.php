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
