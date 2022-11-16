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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotAddProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotDeleteProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
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
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductMultiShopRepository $productMultiShopRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productMultiShopRepository = $productMultiShopRepository;
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
            throw new InvalidShopConstraintException('Image has no features related with shop group use single shop and all shops constraints');
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
        $qb = $this->connection->createQueryBuilder()->select('id_image');

        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Image has no features related with shop group use single shop and all shops constraints');
        } elseif ($shopConstraint->forAllShops()) {
            $qb->from($this->dbPrefix . 'image', 'i')
                ->addOrderBy('i.position', 'ASC')
            ;
        } else {
            $qb->from($this->dbPrefix . 'image_shop', 'i')
                ->andWhere('i.id_shop = :shopId')
                ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        }

        $results = $qb->andWhere('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.id_image', 'ASC')
            ->execute()
            ->fetchAll()
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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'image_shop')
            ->where('id_image = :imageId')
            ->setParameter('imageId', $imageId->getValue())
        ;

        $result = $qb->execute()->fetchAll();
        if (empty($result)) {
            return [];
        }

        $shops = [];
        foreach ($result as $shop) {
            $shops[] = new ShopId((int) $shop['id_shop']);
        }

        return $shops;
    }

    public function create(ProductId $productId, ShopConstraint $shopConstraint): Image
    {
        $productIdValue = $productId->getValue();
        $image = new Image();
        $image->id_product = $productIdValue;
        $image->cover = !Image::getCover($productIdValue);

        $shopIds = $this->productMultiShopRepository->getShopIdsByConstraint($productId, $shopConstraint);
        $this->addObjectModelToShops($image, $shopIds, CannotAddProductImageException::class);

        return $image;
    }

    /**
     * @param Image $image
     * @param ShopId[] $shopsToRemoveImageFrom
     *
     * @return void
     */
    public function deleteFromShops(Image $image, array $shopsToRemoveImageFrom)
    {
        $this->deleteObjectModelFromShops($image, $shopsToRemoveImageFrom, CannotDeleteProductImageException::class);
    }

    /**
     * @param ImageId $imageId
     *
     * @return ShopId[]
     */
    public function getShopIdsCoveredBy(ImageId $imageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'image_shop')
            ->where('id_image = :imageId')
            ->andWhere('cover = 1')
            ->setParameter('imageId', $imageId->getValue())
        ;

        return array_map(
            static function (array $shop): ShopId {
                return new ShopId((int) $shop['id_shop']);
            },
            $qb->execute()->fetchAll()
        );
    }
}
