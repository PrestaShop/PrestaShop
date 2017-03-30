<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Configuration;
use Doctrine\DBAL\Driver\Connection;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Exception\NotImplementedException;
use RuntimeException;
use Shop;

class CategoryRepository
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
    private $languageId;

    /**
     * @var int
     */
    private $shopId;

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

        if (!$context->employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        $languageId = $context->employee->id_lang;
        $this->languageId = (int)$languageId;

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
    public function getCategories()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            c.id_category as category_id,
            cl.name
            FROM {table_prefix}category c
            LEFT JOIN {table_prefix}category_lang cl ON (cl.id_category = c.id_category)
            LEFT JOIN {table_prefix}category_shop cs ON (cs.id_category = c.id_category)
            WHERE cl.id_lang = :language_id
            AND cs.id_shop = :shop_id
        ');

        $statement = $this->connection->prepare($query);

        $statement->bindValue('language_id', $this->languageId);
        $statement->bindValue('shop_id', $this->shopId);

        $statement->execute();

        $rows = $statement->fetchAll();

        return $this->castNumericToInt($rows);
    }
}
