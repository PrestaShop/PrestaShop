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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use Doctrine\DBAL\Connection;
use Image;
use ImageType;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotAddProductImageException;
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
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
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
     * @return Image[]
     *
     * @throws CoreException
     */
    public function getImages(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();

        //@todo: multishop not handled
        $results = $qb->select('id_image')
            ->from($this->dbPrefix . 'image', 'i')
            ->where('i.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->execute()
            ->fetchAll()
        ;

        if (!$results) {
            return [];
        }

        $images = [];
        foreach ($results as $result) {
            $imageId = new ImageId((int) $result['id_image']);
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
}
