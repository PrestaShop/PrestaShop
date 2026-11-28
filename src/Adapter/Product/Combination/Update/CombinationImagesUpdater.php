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
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
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

    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopConstraint|null $shopConstraint null clears the associations of every shop
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function deleteAllImageAssociations(CombinationId $combinationId, ?ShopConstraint $shopConstraint = null): void
    {
        $table = $this->dbPrefix . 'product_attribute_image';
        $qb = $this->connection->createQueryBuilder()
            ->delete($table)
            ->where('id_product_attribute = :combinationId')
            ->setParameter('combinationId', $combinationId->getValue())
        ;

        if (null !== $shopConstraint && !$shopConstraint->forAllShops()) {
            // The association has no shop of its own: it belongs to the shops holding the image
            ProductImageRepository::filterByImageShops($qb, $this->dbPrefix, $table . '.id_image', $shopConstraint);
        }

        $qb->executeStatement();
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
        $this->deleteAllImageAssociations($combinationId, $shopConstraint);

        if (empty($imageIds)) {
            return;
        }

        $imageIdValues = array_map(static fn (ImageId $imageId): int => $imageId->getValue(), $imageIds);
        // An image the constrained shops do not hold survives the delete above
        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . 'product_attribute_image WHERE id_product_attribute = :combinationId AND id_image IN (:imageIds)',
            ['combinationId' => $combinationId->getValue(), 'imageIds' => $imageIdValues],
            ['imageIds' => ArrayParameterType::INTEGER]
        );

        foreach ($imageIdValues as $imageIdValue) {
            $this->connection->insert($this->dbPrefix . 'product_attribute_image', [
                'id_product_attribute' => $combinationId->getValue(),
                'id_image' => $imageIdValue,
            ]);
        }
    }
}
