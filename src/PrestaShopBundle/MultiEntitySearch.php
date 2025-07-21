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

namespace PrestaShopBundle;

use Doctrine\DBAL\Connection;

class MultiEntitySearch
{
    private $connection;
    private $dbPrefix;

    public function __construct(
        Connection $connection,
        String $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    public function searchByTerm(
        string $entityType,
        string $term,
        int $langId,
        int $shopId,
    ) : array {
        $sql = '';

        $params = [
            'shopId' => $shopId,
            'search' => '%' . $term . '%'
        ];

        $sql = $this->getSqlByEntityType($entityType);

        if ($entityType !== 'manufacturer') {
            $params['langId'] = $langId;
        }

        return $this->connection
            ->executeQuery($sql, $params)
            ->fetchAllAssociative();
    }

    private function getSqlByEntityType(
        string $entityType
    ) : String {
        switch ($entityType) {
            case 'product':
                $sql = 'SELECT `name`, ps.`id_product` AS `id`
                        FROM ' . $this->dbPrefix . 'product p
                        INNER JOIN ' . $this->dbPrefix . 'product_shop ps ON ps.id_product = p.id_product
                        INNER JOIN ' . $this->dbPrefix . 'product_lang pl ON pl.id_product = ps.id_product
                        WHERE (`name` LIKE :search OR p.`reference` LIKE :search)
                        AND ps.`id_shop` = :shopId
                        AND pl.`id_shop` = :shopId
                        AND pl.`id_lang` = :langId';
                break;
            case 'category':
                $sql = 'SELECT `name`, cs.`id_category` AS `id`
                        FROM ' . $this->dbPrefix . 'category_shop cs
                        INNER JOIN ' . $this->dbPrefix . 'category_lang cl ON cl.id_category = cs.id_category
                        WHERE `name` LIKE :search AND cs.`id_shop` = :shopId
                        AND cl.`id_shop` = :shopId
                        AND cl.`id_lang` = :langId';
                break;
            case 'cms':
                $sql = 'SELECT `meta_title` AS `name`, cs.`id_cms` AS `id`
                        FROM ' . $this->dbPrefix . 'cms_shop cs
                        INNER JOIN ' . $this->dbPrefix . 'cms_lang cl ON cl.id_cms = cs.id_cms
                        WHERE `meta_title` LIKE :search AND cs.`id_shop` = :shopId
                        AND cl.`id_shop` = :shopId
                        AND cl.`id_lang` = :langId';
                break;
            case 'cms_category':
                $sql = 'SELECT `name`, cs.`id_cms_category` AS `id`
                        FROM ' . $this->dbPrefix . 'cms_category_shop cs
                        INNER JOIN ' . $this->dbPrefix . 'cms_category_lang cl ON cl.id_cms_category = cs.id_cms_category
                        WHERE `name` LIKE :search AND cs.`id_shop` = :shopId
                        AND cl.`id_shop` = :shopId
                        AND cl.`id_lang` = :langId';
                break;
            case 'manufacturer':
                $sql = 'SELECT `name`, m.`id_manufacturer` AS `id`
                        FROM ' . $this->dbPrefix . 'manufacturer m
                        INNER JOIN ' . $this->dbPrefix . 'manufacturer_shop ms ON ms.id_manufacturer = m.id_manufacturer
                        WHERE `name` LIKE :search AND ms.`id_shop` = :shopId';
                break;
        }

        return $sql;
    }
}
