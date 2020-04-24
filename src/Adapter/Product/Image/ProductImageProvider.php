<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use Db;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class ProductImageProvider
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
    )
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @todo: multistore - single shop, group, all shops. combination image?
     * @param int $productId
     *
     * @return
     */
    public function getImages(int $productId): array
    {
        //@todo: I might need to get lang fields in separate sql query
        $qb = $this->connection->createQueryBuilder();
        $qb->select('i.id_image, i.id_product, i.position, i.cover, il.legend, il.id_lang')
            ->from($this->dbPrefix . 'image', 'i')
            ->leftJoin(
                'i',
                $this->dbPrefix . 'image_lang',
                'il',
                'i.id_image = il.id_image'
            )
            ->where('id_product = :productId')
            ->groupBy('il.id_lang')
            ->setParameter('productId', $productId)
        ;

        return $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }

}
