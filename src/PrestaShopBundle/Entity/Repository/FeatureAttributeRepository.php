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

use Doctrine\DBAL\Driver\Connection;
use Employee;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use RuntimeException;

class FeatureAttributeRepository
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
     * FeatureAttributeRepository constructor.
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
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            a.id_attribute_group AS attribute_group_id,
            agl.name AS name,
            GROUP_CONCAT(
              CONCAT(al.id_attribute, ":", al.name)
            ) AS "values"
            FROM {table_prefix}attribute a
            LEFT JOIN {table_prefix}attribute_lang al ON (
                a.id_attribute = al.id_attribute
                AND al.id_lang = :language_id
                AND LENGTH(TRIM(al.name)) > 0
            )
            LEFT JOIN {table_prefix}attribute_group ag ON (
                ag.id_attribute_group = a.id_attribute_group
            )
            LEFT JOIN {table_prefix}attribute_group_lang agl ON (
                ag.id_attribute_group = agl.id_attribute_group
                AND agl.id_lang = :language_id
                AND LENGTH(TRIM(agl.name)) > 0
            )
            GROUP BY ag.id_attribute_group
        ');

        $statement = $this->connection->prepare($query);

        $statement->bindValue('language_id', $this->languageId);

        $statement->execute();

        $rows = $statement->fetchAll();
        $rows = $this->explodeCollections($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param $rows
     * @return array
     */
    private function explodeCollections($rows)
    {
        return array_map(function ($row) {
            $row['values'] = explode(',', $row['values']);

            $row['values'] = array_map(function ($value) {
                $parts = explode(':', $value);

                return array(
                  'attribute_id' => $parts[0],
                  'name' => $parts[1],
                );
            }, $row['values']);

            $row['values'] = $this->castNumericToInt($row['values']);

            return $row;
        }, $rows);
    }
}
