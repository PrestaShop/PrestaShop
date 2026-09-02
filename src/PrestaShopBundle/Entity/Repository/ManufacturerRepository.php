<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Exception\NotImplementedException;
use RuntimeException;
use Shop;

class ManufacturerRepository
{
    use NormalizeFieldTrait;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param string $tablePrefix
     *
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        $tablePrefix
    ) {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;

        $context = $contextAdapter->getContext();

        if (!$context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $context->shop;
        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->shopId = $shop->getContextualShopId();
    }

    /**
     * @return mixed
     */
    public function getManufacturers()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            m.id_manufacturer AS manufacturer_id,
            m.name
            FROM {table_prefix}manufacturer m
            INNER JOIN {table_prefix}manufacturer_shop ms ON (
                ms.id_shop = :shop_id AND
                ms.id_manufacturer = m.id_manufacturer
            )'
        );

        $statement = $this->connection->prepare($query);

        $statement->bindValue('shop_id', $this->shopId);

        $result = $statement->executeQuery();

        $rows = $result->fetchAllAssociative();

        return $this->castNumericToInt($rows);
    }
}
