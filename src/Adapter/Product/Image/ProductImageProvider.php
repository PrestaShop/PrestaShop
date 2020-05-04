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
     * @param int $productId
     *
     * @return array
     * @todo: handle multistore - single shop, group, all shops. combination image?
     */
    public function getImages(int $productId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('i.id_image, i.id_product, i.position, i.cover')
            ->from($this->dbPrefix . 'image', 'i')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('position', 'asc')
        ;

        $images = $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);
        $localizedLegends = $this->getLocalizedLegends($productId);

        foreach ($images as &$image) {
            foreach ($localizedLegends as $legend) {
                if ($legend['id_image'] === $image['id_image']) {
                    $image['legend'][$legend['id_lang']] = $legend['legend'];
                }
            }
        }

        return $images;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    private function getLocalizedLegends(int $productId): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('il.legend, il.id_lang, il.id_image')
            ->from($this->dbPrefix . 'image_lang', 'il')
            ->innerJoin(
                'il',
                $this->dbPrefix . 'image',
                'i',
                'i.id_product = :productId'
            )
            ->setParameter('productId', $productId)
        ;

        return $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }
}
