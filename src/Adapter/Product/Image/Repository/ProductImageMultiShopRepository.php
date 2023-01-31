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
use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\Validate\ProductImageValidator;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
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
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * todo: merge this repository with ProductImageRepository
 * Provides access to product Image data source with shop context
 */
class ProductImageMultiShopRepository extends AbstractMultiShopObjectModelRepository
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
     * @var ProductMultiShopRepository
     */
    private $productMultiShopRepository;

    /**
     * @var ProductImageValidator
     */
    private $productImageValidator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductMultiShopRepository $productMultiShopRepository,
        ProductImageValidator $productImageValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productMultiShopRepository = $productMultiShopRepository;
        $this->productImageValidator = $productImageValidator;
    }

    /**
     * @param ProductId $productId
     *
     * @return Image[]
     *
     * @throws CoreException
     */
    public function getImages(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException(sprintf('%s::getImages does not handle shop group constraint', self::class));
        } elseif ($shopConstraint->forAllShops()) {
            $shopId = $this->productMultiShopRepository->getProductDefaultShopId($productId);
        } else {
            $shopId = $shopConstraint->getShopId();
        }

        return array_map(
            function (ImageId $imageId) use ($shopId): Image {
                return $this->get($imageId, $shopId);
            },
            $this->getImagesIds($productId, $shopConstraint)
        );
    }

    /**
     * @param ProductId $productId
     *
     * @return ImageId[]
     *
     * @throws CoreException
     */
    public function getImagesIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder()->select('i.id_image');

        if ($shopConstraint->getShopGroupId()) {
            $qb->from($this->dbPrefix . 'image_shop', 'i')
                ->innerJoin(
                    'i',
                    $this->dbPrefix . 'shop',
                    's',
                    's.id_shop = i.id_shop AND s.id_shop_group = :shopGroupId'
                )
                ->setParameter('shopGroupId', $shopConstraint->getShopGroupId()->getValue())
            ;
        } elseif ($shopConstraint->getShopId()) {
            $qb->from($this->dbPrefix . 'image_shop', 'i')
                ->andWhere('i.id_shop = :shopId')
                ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        } else {
            $qb->from($this->dbPrefix . 'image', 'i')
                ->addOrderBy('i.position', 'ASC')
            ;
        }

        $results = $qb->andWhere('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.id_image', 'ASC')
            ->execute()
            ->fetchAllAssociative()
        ;

        if (!$results) {
            return [];
        }

        $imagesIds = [];
        foreach ($results as $result) {
            $imagesIds[] = new ImageId((int) $result['id_image']);
        }

        return $imagesIds;
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

        $shopIds = $this->productMultiShopRepository->getShopIdsByConstraint($productId, $shopConstraint);
        $this->addObjectModelToShops($image, $shopIds, CannotAddProductImageException::class);

        $this->updateMissingCovers($productId);

        return $image;
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

        foreach ($this->productMultiShopRepository->getAssociatedShopIds($productId) as $shopId) {
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

    public function getCoverImageId(ProductId $productId, ShopId $shopId): ?ImageId
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
            $coverId = $this->getCoverImageId($productId, new ShopId((int) $image['id_shop']));
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
}
