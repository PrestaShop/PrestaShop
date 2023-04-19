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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Image;
use ImageType;
use PrestaShop\PrestaShop\Adapter\Product\Image\Validate\ProductImageValidator;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotAddProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotDeleteProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUpdateProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopImageAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopImageAssociationCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImagesCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShopException;

/**
 * Provides access to product Image data source with shop context
 */
class ProductImageRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageValidator
     */
    private $productImageValidator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductRepository $productRepository,
        ProductImageValidator $productImageValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productRepository = $productRepository;
        $this->productImageValidator = $productImageValidator;
    }

    /**
     * @param ProductId $productId
     *
     * @return Image[]
     */
    public function getImages(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop group constraint is not supported');
        }

        if ($shopConstraint->getShopId()) {
            $this->productRepository->assertProductIsAssociatedToShop($productId, $shopConstraint->getShopId());
        }

        return array_map(
            function (ImageId $imageId) use ($shopConstraint): Image {
                return $this->getByShopConstraint($imageId, $shopConstraint);
            },
            $this->getImageIds($productId, $shopConstraint)
        );
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return ImageId[]
     */
    public function getImageIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('i.id_image')
            ->from($this->dbPrefix . 'image', 'i')
            ->andWhere('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.position', 'ASC')
            ->addOrderBy('i.id_image', 'ASC')
        ;

        if (!$shopConstraint->forAllShops()) {
            $qb
                ->innerJoin(
                'i',
                $this->dbPrefix . 'image_shop',
                'img_shop',
                'img_shop.id_image = i.id_image'
                )
                ->addGroupBy('i.id_image')
            ;

            if ($shopConstraint->getShopGroupId()) {
                $qb
                    ->innerJoin(
                        'img_shop',
                        $this->dbPrefix . 'shop',
                        's',
                        's.id_shop = img_shop.id_shop AND s.id_shop_group = :shopGroupId'
                    )
                    ->setParameter('shopGroupId', $shopConstraint->getShopGroupId()->getValue())
                ;
            } else {
                $this->productRepository->assertProductIsAssociatedToShop($productId, $shopConstraint->getShopId());
                $qb->andWhere('img_shop.id_shop = :shopId')
                    ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
                ;
            }
        }

        return array_map(static function (string $id): ImageId {
            return new ImageId((int) $id);
        }, $qb->execute()->fetchAll(FetchMode::COLUMN));
    }

    /**
     * @param ProductId $productId
     *
     * @return ImageId|null
     */
    public function getDefaultImageId(ProductId $productId, ShopId $shopId): ?ImageId
    {
        $coverId = $this->findCoverId($productId, $shopId);
        if ($coverId) {
            return $coverId;
        }

        $imagesIds = $this->getImageIds($productId, ShopConstraint::shop($shopId->getValue()));

        return !empty($imagesIds) ? reset($imagesIds) : null;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return ImageId|null
     */
    public function findCoverId(ProductId $productId, ShopId $shopId): ?ImageId
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('i.id_image')
            ->from($this->dbPrefix . 'image_shop', 'i')
            ->andWhere('i.id_product = :productId')
            ->andWhere('i.cover = 1')
            ->andWhere('i.id_shop = :shopId')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('shopId', $shopId->getValue())
        ;
        $result = $qb->execute()->fetchAssociative();

        if (empty($result['id_image'])) {
            return null;
        }

        return new ImageId((int) $result['id_image']);
    }

    /**
     * Retrieves a list of image ids ordered by position for each provided combination id
     *
     * @param CombinationId[] $combinationIds
     *
     * @return array<int, ImageId[]> [(int) id_combination => [ImageId]]
     */
    public function getImageIdsForCombinations(array $combinationIds): array
    {
        if (empty($combinationIds)) {
            return [];
        }

        $combinationIds = array_map(function (CombinationId $id): int {
            return $id->getValue();
        }, $combinationIds);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pai.id_product_attribute, pai.id_image')
            ->from($this->dbPrefix . 'product_attribute_image', 'pai')
            ->leftJoin(
                'pai',
                $this->dbPrefix . 'image', 'i',
                'i.id_image = pai.id_image'
            )
            ->andWhere($qb->expr()->in('pai.id_product_attribute', ':combinationIds'))
            ->andWhere('pai.id_image != 0')
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
            ->orderBy('i.position', 'asc')
        ;

        $results = $qb->execute()->fetchAll();

        if (empty($results)) {
            return [];
        }

        // Temporary ImageId pool to avoid creating duplicates
        $imageIds = [];
        $imagesIdsByCombinationIds = [];
        foreach ($results as $result) {
            $id = (int) $result['id_image'];
            if (!isset($imageIds[$id])) {
                $imageIds[$id] = new ImageId($id);
            }
            $imagesIdsByCombinationIds[(int) $result['id_product_attribute']][] = $imageIds[$id];
        }

        return $imagesIdsByCombinationIds;
    }

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws CoreException
     */
    public function get(ImageId $imageId, ShopId $shopId): Image
    {
        /** @var Image $image */
        $image = $this->getObjectModelForShop(
            $imageId->getValue(),
            Image::class,
            ProductImageNotFoundException::class,
            $shopId
        );

        return $image;
    }

    public function getByShopConstraint(ImageId $imageId, ShopConstraint $shopConstraint): Image
    {
        if ($shopConstraint->getShopId()) {
            return $this->get($imageId, $shopConstraint->getShopId());
        }

        $shopIds = $this->getAssociatedShopIdsByShopConstraint($imageId, $shopConstraint);
        // finds first associated shop and uses it to load object model
        $shopId = reset($shopIds);

        if (!$shopId) {
            throw new ShopAssociationNotFound(sprintf('Image %d is not associated to any shop', $imageId->getValue()));
        }

        return $this->get($imageId, $shopId);
    }

    /**
     * @param ImageId $imageId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIds(ImageId $imageId): array
    {
        return array_map(
            static function (array $shop): ShopId {
                return new ShopId((int) $shop['id_shop']);
            },
            $this->connection->createQueryBuilder()
                ->select('id_shop')
                ->from($this->dbPrefix . 'image_shop')
                ->where('id_image = :imageId')
                ->setParameter('imageId', $imageId->getValue())
                ->execute()
                ->fetchAllAssociative()
        );
    }

    /**
     * @param ImageId $imageId
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsByShopConstraint(ImageId $imageId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('is.id_shop')
            ->from($this->dbPrefix . 'image_shop', '`is`')
            ->where('is.id_image = :imageId')
            ->setParameter('imageId', $imageId->getValue())
        ;

        if ($shopConstraint->getShopGroupId()) {
            $qb
                ->innerJoin(
                    '`is`',
                    $this->dbPrefix . 'shop',
                    's',
                    's.id_shop = is.id_shop AND s.id_shop_group = :shopGroupId'
                )
                ->setParameter('shopGroupId', $shopConstraint->getShopGroupId()->getValue())
            ;
        } elseif ($shopConstraint->getShopId()) {
            $qb
                ->andWhere('is.id_shop = :shopId')
                ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        }

        return array_map(static function (array $shop): ShopId {
            return new ShopId((int) $shop['id_shop']);
        }, $qb->execute()->fetchAllAssociative());
    }

    public function create(ProductId $productId, ShopConstraint $shopConstraint): Image
    {
        $productIdValue = $productId->getValue();
        $image = new Image();
        $image->id_product = $productIdValue;
        $image->cover = null;

        $shopIds = $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint);
        $this->addObjectModelToShops($image, $shopIds, CannotAddProductImageException::class);

        $this->updateMissingCovers($productId);

        return $image;
    }

    /**
     * Duplicate an image and associates it to another product, the same shop association are kept based on
     * specified shop constraint. Unles the image is associated to no shops matching the shop constraint, in
     * which case no duplication is done and null is returned.
     *
     * @param ImageId $sourceImageId
     * @param ProductId $newProductId
     * @param ShopConstraint $shopConstraint
     *
     * @return Image|null
     *
     * @throws CoreException
     */
    public function duplicate(ImageId $sourceImageId, ProductId $newProductId, ShopConstraint $shopConstraint): ?Image
    {
        $associatedShopIds = $this->getAssociatedShopIdsByShopConstraint($sourceImageId, $shopConstraint);
        if (empty($associatedShopIds)) {
            return null;
        }

        $sourceImage = $this->getImageById($sourceImageId);
        $newImage = clone $sourceImage;
        unset($newImage->id, $newImage->id_image);
        $newImage->id_product = $newProductId->getValue();
        $newImage->cover = $sourceImage->cover;

        $this->addObjectModelToShops($newImage, $associatedShopIds, CannotAddProductImageException::class);

        return $newImage;
    }

    /**
     * @param ImageId $imageId
     * @param ShopId[] $shopIds
     *
     * @return void
     */
    public function deleteFromShops(ImageId $imageId, array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $this->checkShopAssociation($imageId->getValue(), Image::class, $shopId);
        }

        $this->deleteObjectModelFromShops(
            // We fetch the image from first shop, the values don't matter anyway we just need an Image instance
            $this->get($imageId, reset($shopIds)),
            $shopIds,
            CannotDeleteProductImageException::class
        );
    }

    /**
     * @param ImageId $imageId
     * @param ShopConstraint $shopConstraint
     *
     * @return void
     */
    public function deleteByShopConstraint(ImageId $imageId, ShopConstraint $shopConstraint): void
    {
        $shopIds = $this->getAssociatedShopIdsByShopConstraint($imageId, $shopConstraint);
        if (empty($shopIds)) {
            return;
        }

        $this->deleteObjectModelFromShops(
        // We fetch the image from first shop, the values don't matter anyway we just need an Image instance
            $this->get($imageId, reset($shopIds)),
            $shopIds,
            CannotDeleteProductImageException::class
        );
    }

    /**
     * @param ImageId $imageId
     *
     * @return ShopId[]
     */
    public function getShopIdsByCoverId(ImageId $imageId): array
    {
        $results = $this->connection->createQueryBuilder()
            ->select('id_shop')
            ->from($this->dbPrefix . 'image_shop')
            ->where('id_image = :imageId')
            ->andWhere('cover = 1')
            ->setParameter('imageId', $imageId->getValue())
            ->execute()
            ->fetchAll()
        ;

        return array_map(
            static function (array $shop): ShopId {
                return new ShopId((int) $shop['id_shop']);
            },
            $results
        );
    }

    /**
     * @param ProductId $productId
     *
     * @return ShopProductImagesCollection
     */
    public function getImagesFromAllShop(ProductId $productId): ShopProductImagesCollection
    {
        $results = $this->connection->createQueryBuilder()
            ->select('id_image', 'id_shop', 'cover')
            ->from($this->dbPrefix . 'image_shop', 'i')
            ->andWhere('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.id_shop', 'ASC')
            ->addOrderBy('i.id_image', 'ASC')
            ->execute()
            ->fetchAll()
        ;

        $productImagesByShop = [];
        foreach ($results as $result) {
            $shopId = (int) $result['id_shop'];
            $productImagesByShop[$shopId][] = new ShopImageAssociation((int) $result['id_image'], (int) $result['cover'] === 1);
        }

        foreach ($this->productRepository->getAssociatedShopIds($productId) as $shopId) {
            if (isset($productImagesByShop[$shopId->getValue()])) {
                continue;
            }
            $productImagesByShop[$shopId->getValue()] = [];
        }

        $shopProductImagesArray = array_map(
            function (int $shopId, array $productImages): ShopProductImages {
                return new ShopProductImages($shopId, ShopImageAssociationCollection::from(...$productImages));
            },
            array_keys($productImagesByShop),
            $productImagesByShop
        );

        return ShopProductImagesCollection::from(...$shopProductImagesArray);
    }

    public function findCoverImageId(ProductId $productId, ShopId $shopId): ?ImageId
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id_image')
            ->from($this->dbPrefix . 'image_shop')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->andWhere('id_shop = :shopId')
            ->setParameter('shopId', $shopId->getValue())
            ->andWhere('cover = 1')
            ->execute()
            ->fetchOne()
            ;

        return $result ? new ImageId((int) $result) : null;
    }

    public function findCoverImageIdGlobal(ProductId $productId): ?ImageId
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id_image')
            ->from($this->dbPrefix . 'image')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->andWhere('cover = 1')
            ->execute()
            ->fetchOne()
        ;

        return $result ? new ImageId((int) $result) : null;
    }

    public function associateImageToShop(Image $image, ShopId $shopId): void
    {
        $this->connection->createQueryBuilder()
            ->insert($this->dbPrefix . 'image_shop')
            ->values(
                [
                    'id_product' => ':productId',
                    'id_image' => ':imageId',
                    'id_shop' => ':shopId',
                    'cover' => ':cover',
                ]
            )
            ->setParameter('productId', (int) $image->id_product)
            ->setParameter('imageId', (int) $image->id)
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('cover', $image->cover ? 1 : null)
            ->execute()
        ;
    }

    public function updateMissingCovers(ProductId $productId): void
    {
        $results = $this->connection->createQueryBuilder()
            ->select('is.id_image', 'is.id_shop', 'is.cover')
            ->from($this->dbPrefix . 'image_shop', '`is`')
            ->leftJoin(
                '`is`',
                $this->dbPrefix . 'image',
                'i',
                'i.id_image = is.id_image'
            )
            ->andWhere('is.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.position', 'ASC')
            ->execute()
            ->fetchAll()
        ;

        foreach ($results as $image) {
            $coverId = $this->findCoverImageId($productId, new ShopId((int) $image['id_shop']));
            $coverIdGlobal = $this->findCoverImageIdGlobal($productId);
            if ($coverId !== null && $coverId->getValue() === (int) $image['id_image']) {
                continue;
            }

            if ($coverId === null) {
                $newValue = 1;
            } else {
                $newValue = null;
            }

            if ($newValue === $image['cover']) {
                continue;
            }
            $this->connection->createQueryBuilder()
                ->update($this->dbPrefix . 'image_shop')
                ->set($this->dbPrefix . 'image_shop' . '.cover', ':cover')
                ->setParameter('cover', $newValue)
                ->andWhere($this->dbPrefix . 'image_shop' . '.id_image = :imageId')
                ->setParameter('imageId', (int) $image['id_image'])
                ->andWhere($this->dbPrefix . 'image_shop' . '.id_shop = :shopId')
                ->setParameter('shopId', (int) $image['id_shop'])
                ->execute()
            ;

            if ($coverIdGlobal === null) {
                $this->connection->createQueryBuilder()
                    ->update($this->dbPrefix . 'image')
                    ->set('cover', ':cover')
                    ->setParameter('cover', $newValue)
                    ->andWhere('id_image = :imageId')
                    ->setParameter('imageId', (int) $image['id_image'])
                    ->execute()
                ;
            }
        }
    }

    /**
     * @param array<int|string, string|int[]> $updatableProperties
     * @param ShopId[] $shopIds
     */
    public function partialUpdateForShops(Image $image, array $updatableProperties, array $shopIds, int $errorCode = 0): void
    {
        $this->productImageValidator->validate($image);
        $this->partiallyUpdateObjectModelForShops(
            $image,
            $updatableProperties,
            $shopIds,
            CannotUpdateProductImageException::class,
            $errorCode
        );
    }

    public function delete(Image $image): void
    {
        $this->deleteObjectModelFromShops(
            $image,
            $this->getAssociatedShopIds(new ImageId((int) $image->id)),
            CannotDeleteProductImageException::class
        );
    }

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws CoreException
     */
    public function getImageById(ImageId $imageId): Image
    {
        /** @var Image $image */
        $image = $this->getObjectModel(
            $imageId->getValue(),
            Image::class,
            ProductImageNotFoundException::class
        );

        return $image;
    }

    /**
     * @return ImageType[]
     */
    public function getProductImageTypes(): array
    {
        try {
            $results = ImageType::getImagesTypes('products');
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to get product image types');
        }

        if (!$results) {
            return [];
        }

        $imageTypes = [];
        foreach ($results as $result) {
            $imageType = new ImageType();
            $imageType->id = (int) $result['id_image_type'];
            $imageType->name = $result['name'];
            $imageType->width = (int) $result['width'];
            $imageType->height = (int) $result['height'];
            $imageType->products = (bool) $result['products'];
            $imageType->categories = (bool) $result['categories'];
            $imageType->manufacturers = (bool) $result['manufacturers'];
            $imageType->suppliers = (bool) $result['suppliers'];
            $imageType->stores = (bool) $result['stores'];
            $imageTypes[] = $imageType;
        }

        return $imageTypes;
    }

    public function getPreviewCombinationProduct(CombinationId $combinationId): ?ImageId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pai.id_image')
            ->from($this->dbPrefix . 'product_attribute_image', 'pai')
            ->leftJoin('pai', $this->dbPrefix . 'image', 'i', 'i.id_image = pai.id_image')
            ->where('pai.id_product_attribute = :productAttribute')
            ->orderBy('i.cover', 'DESC')
            ->setMaxResults(1)
            ->setParameter('productAttribute', $combinationId->getValue());
        $data = $qb->execute()->fetchOne();
        if ($data > 0) {
            return new ImageId((int) $data);
        }

        return null;
    }
}
