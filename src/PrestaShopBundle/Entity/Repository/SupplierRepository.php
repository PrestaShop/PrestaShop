<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Connection;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
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
     * @var string
     */
    private $tablePrefix;

    /**
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param $tablePrefix
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        $tablePrefix
    )
    {
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

        $statement->execute();

        $rows = $statement->fetchAll();

        return $this->castNumericToInt($rows);
    }
}
