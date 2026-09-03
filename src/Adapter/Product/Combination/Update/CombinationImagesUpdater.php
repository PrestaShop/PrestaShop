<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates images associated to combination
 */
class CombinationImagesUpdater
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
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ShopRepository $shopRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint|null $shopConstraint Null clears every shop, which is what callers with
     *                                            no shop context have always done
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function deleteAllImageAssociations(CombinationId $combinationId, ?ShopConstraint $shopConstraint = null): void
    {
        if (null === $shopConstraint || $shopConstraint->forAllShops()) {
            $this->connection->delete(
                $this->dbPrefix . 'product_attribute_image',
                ['id_product_attribute' => $combinationId->getValue()]
            );

            return;
        }

        // WHY: the selection belongs to the shop it was made in. Clearing every shop's rows is what
        // made a save in one shop discard what the others had chosen for the same combination.
        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . 'product_attribute_image
             WHERE id_product_attribute = :combinationId AND id_shop IN (:shopIds)',
            [
                'combinationId' => $combinationId->getValue(),
                'shopIds' => $this->shopRepository->getAssociatedShopIds($shopConstraint),
            ],
            ['shopIds' => ArrayParameterType::INTEGER]
        );
    }

    /**
     * @param CombinationId $combinationId
     * @param ImageId[] $imageIds
     * @param ShopConstraint|null $shopConstraint
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function associateImages(CombinationId $combinationId, array $imageIds, ?ShopConstraint $shopConstraint = null): void
    {
        // First delete the images this shop is responsible for
        $this->deleteAllImageAssociations($combinationId, $shopConstraint);

        $shopIds = null === $shopConstraint || $shopConstraint->forAllShops()
            ? $this->shopRepository->getAllShopIds()
            : $this->shopRepository->getAssociatedShopIds($shopConstraint);

        // Then create all new ones, once per shop the selection was made for
        foreach ($imageIds as $imageId) {
            foreach ($shopIds as $shopId) {
                $this->connection->insert($this->dbPrefix . 'product_attribute_image', [
                    'id_product_attribute' => $combinationId->getValue(),
                    'id_image' => $imageId->getValue(),
                    'id_shop' => $shopId,
                ]);
            }
        }
    }
}
