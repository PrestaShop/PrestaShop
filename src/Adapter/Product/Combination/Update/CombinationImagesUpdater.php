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
