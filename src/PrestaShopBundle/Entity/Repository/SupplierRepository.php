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

class SupplierRepository
{
    use NormalizeFieldTrait;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var int
     */
    public $shopId;

    /**
     * @var string
     */
    private $tablePrefix;

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
    public function getSuppliers()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            s.id_supplier AS supplier_id,
            s.name
            FROM {table_prefix}supplier s
            INNER JOIN {table_prefix}supplier_shop ss ON (
                ss.id_shop = :shop_id AND
                ss.id_supplier = s.id_supplier
            )'
        );

        $statement = $this->connection->prepare($query);

        $statement->bindValue('shop_id', $this->shopId);

        $result = $statement->executeQuery();

        $rows = $result->fetchAllAssociative();

        return $this->castNumericToInt($rows);
    }
}
