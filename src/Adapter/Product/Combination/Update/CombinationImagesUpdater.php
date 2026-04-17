<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

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
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function deleteAllImageAssociations(CombinationId $combinationId): void
    {
        $this->connection->delete(
            $this->dbPrefix . 'product_attribute_image',
            ['id_product_attribute' => $combinationId->getValue()]
        );
    }

    /**
     * @param CombinationId $combinationId
     * @param ImageId[] $imageIds
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function associateImages(CombinationId $combinationId, array $imageIds): void
    {
        // First delete all images
        $this->deleteAllImageAssociations($combinationId);

        // Then create all new ones
        foreach ($imageIds as $imageId) {
            $insertedValues = [
                'id_product_attribute' => $combinationId->getValue(),
                'id_image' => $imageId->getValue(),
            ];
            $this->connection->insert($this->dbPrefix . 'product_attribute_image', $insertedValues);
        }
    }
}
