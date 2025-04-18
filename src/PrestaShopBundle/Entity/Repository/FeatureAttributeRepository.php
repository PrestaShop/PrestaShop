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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Exception\NotImplementedException;
use RuntimeException;
use Shop;

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
     * @var int
     */
    private $shopId;

    /**
     * FeatureAttributeRepository constructor.
     *
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

        if (!$context->employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        $languageId = $context->employee->id_lang;
        $this->languageId = (int) $languageId;

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
              ORDER BY al.id_attribute
            ) AS "values"
            FROM {table_prefix}attribute a
            LEFT JOIN {table_prefix}attribute_shop ats ON (
                ats.id_attribute = a.id_attribute AND
                ats.id_shop = :shop_id
            )
            LEFT JOIN {table_prefix}attribute_lang al ON (
                a.id_attribute = al.id_attribute
                AND al.id_lang = :language_id
                AND LENGTH(TRIM(al.name)) > 0
            )
            LEFT JOIN {table_prefix}attribute_group ag ON (
                ag.id_attribute_group = a.id_attribute_group
            )
            LEFT JOIN {table_prefix}attribute_group_shop ags ON (
                ags.id_attribute_group = a.id_attribute_group AND
                ags.id_shop = :shop_id
            )
            LEFT JOIN {table_prefix}attribute_group_lang agl ON (
                ag.id_attribute_group = agl.id_attribute_group
                AND agl.id_lang = :language_id
                AND LENGTH(TRIM(agl.name)) > 0
            )
            GROUP BY ag.id_attribute_group
        '
        );

        $statement = $this->connection->prepare($query);

        $statement->bindValue('language_id', $this->languageId);
        $statement->bindValue('shop_id', $this->shopId);

        $result = $statement->executeQuery();

        $rows = $result->fetchAllAssociative();
        $rows = $this->explodeCollections($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @return mixed
     */
    public function getFeatures()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            f.id_feature AS feature_id,
            fl.name AS name,
            GROUP_CONCAT(
              CONCAT(fvl.id_feature_value, ":", fvl.value)
              ORDER BY fvl.id_feature_value
            ) AS "values"
            FROM {table_prefix}feature f
            LEFT JOIN {table_prefix}feature_lang fl ON (
                f.id_feature = fl.id_feature AND
                fl.id_lang = :language_id
            )
            LEFT JOIN {table_prefix}feature_shop fs ON (
                fs.id_shop = :shop_id AND
                fs.id_feature = f.id_feature
            )
            LEFT JOIN {table_prefix}feature_value fv ON (
                f.id_feature = fv.id_feature
            )
            LEFT JOIN {table_prefix}feature_value_lang fvl ON (
                fvl.id_lang = :language_id AND
                fvl.id_feature_value = fv.id_feature_value
            )
            WHERE fv.custom = 0
            GROUP BY fv.id_feature
            ORDER BY f.id_feature
        '
        );

        $statement = $this->connection->prepare($query);

        $statement->bindValue('language_id', $this->languageId);
        $statement->bindValue('shop_id', $this->shopId);

        $result = $statement->executeQuery();

        $rows = $result->fetchAllAssociative();
        $rows = $this->explodeCollections($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function explodeCollections($rows)
    {
        return array_map(function ($row) {
            $row['values'] = explode(',', $row['values']);

            $row['values'] = array_map(function ($value) {
                if (!str_contains($value, ':')) {
                    return $value;
                }

                $parts = explode(':', $value);

                return [
                    'item_id' => $parts[0],
                    'name' => $parts[1],
                ];
            }, $row['values']);

            $row['values'] = $this->castNumericToInt($row['values']);

            return $row;
        }, $rows);
    }
}
