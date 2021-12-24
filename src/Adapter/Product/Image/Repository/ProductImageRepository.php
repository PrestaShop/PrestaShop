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
use ImageType;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Validate\ProductImageValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotAddProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotDeleteProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUpdateProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * Provides access to product Image data source
 */
class ProductImageRepository extends AbstractObjectModelRepository
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
     * @var ProductImageValidator
     */
    private $productImageValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductImageValidator $productImageValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductImageValidator $productImageValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productImageValidator = $productImageValidator;
    }

    /**
     * @param ProductId $productId
     * @param int[] $shopIds
     *
     * @return Image
     *
     * @throws CoreException
     * @throws ProductImageException
     * @throws CannotAddProductImageException
     */
    public function create(ProductId $productId, array $shopIds): Image
    {
        $productIdValue = $productId->getValue();
        $image = new Image();
        $image->id_product = $productIdValue;
        $image->cover = !Image::getCover($productIdValue);

        $this->addObjectModel($image, CannotAddProductImageException::class);

        try {
            if (!$image->associateTo($shopIds)) {
                throw new ProductImageException(sprintf(
                    'Failed to associate product image #%d with shops',
                    $image->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to associate image #%d with shops', $image->id),
                0,
                $e
            );
        }

        return $image;
    }

    /**
     * @param ProductId $productId
     *
     * @return ImageId[]
     *
     * @throws CoreException
     */
    public function getImagesIds(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();

        //@todo: multishop not handled
        $results = $qb->select('id_image')
            ->from($this->dbPrefix . 'image', 'i')
            ->where('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->addOrderBy('i.position', 'ASC')
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
     * @param ProductId $productId
     *
     * @return ImageId|null
     *
     * @throws CoreException
     */
    public function getDefaultImageId(ProductId $productId): ?ImageId
    {
        $coverId = $this->findCoverId($productId);
        if ($coverId) {
            return $coverId;
        }

        $imagesIds = $this->getImagesIds($productId);

        return !empty($imagesIds) ? reset($imagesIds) : null;
    }

    /**
     * @param ProductId $productId
     *
     * @return Image[]
     *
     * @throws CoreException
     */
    public function getImages(ProductId $productId): array
    {
        $imagesIds = $this->getImagesIds($productId);

        if (empty($imagesIds)) {
            return [];
        }

        $images = [];
        foreach ($imagesIds as $imageId) {
            $images[] = $this->get($imageId);
        }

        return $images;
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

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws CoreException
     */
    public function get(ImageId $imageId): Image
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
     * @param ProductId $productId
     *
     * @return ImageId|null
     *
     * @throws CoreException
     */
    public function findCoverId(ProductId $productId): ?ImageId
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->addSelect('i.id_image')
                ->from($this->dbPrefix . 'image', 'i')
                ->andWhere('i.id_product = :productId')
                ->andWhere('i.cover = 1')
                ->setParameter('productId', $productId->getValue())
            ;
            $result = $qb->execute()->fetch();
            $id = !empty($result['id_image']) ? (int) $result['id_image'] : null;
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred while trying to get product default combination', 0, $e);
        }

        return $id ? new ImageId($id) : null;
    }

    /**
     * @param ProductId $productId
     *
     * @return Image|null
     *
     * @throws CoreException
     */
    public function findCover(ProductId $productId): ?Image
    {
        $imageId = $this->findCoverId($productId);

        return $imageId ? $this->get($imageId) : null;
    }

    /**
     * Retrieves a list of image ids ordered by position for each provided combination id
     *
     * @param int[] $combinationIds
     *
     * @return array<int, ImageId[]> [(int) id_combination => [ImageId]]
     */
    public function getImagesIdsForCombinations(array $combinationIds): array
    {
        //@todo: multishop not handled
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
     * @param Image $image
     * @param array $updatableProperties
     * @param int $errorCode
     *
     * @throws CannotUpdateProductImageException
     */
    public function partialUpdate(Image $image, array $updatableProperties, int $errorCode = 0): void
    {
        $this->productImageValidator->validate($image);
        $this->partiallyUpdateObjectModel(
            $image,
            $updatableProperties,
            CannotUpdateProductImageException::class,
            $errorCode
        );
    }

    /**
     * @param Image $image
     *
     * @throws CannotDeleteProductImageException
     */
    public function delete(Image $image): void
    {
        $this->deleteObjectModel($image, CannotDeleteProductImageException::class);
    }
}
