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

namespace PrestaShop\PrestaShop\Adapter\Attribute\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use RuntimeException;

/**
 * Provides access to attribute data source
 */
class AttributeRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param int[] $attributeIds
     */
    public function assertAllAttributesExist(array $attributeIds): void
    {
        if (empty($attributeIds)) {
            throw new RuntimeException('Empty list of attribute ids provided');
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id_attribute) AS total')
            ->from($this->dbPrefix . 'attribute')
            ->where('id_attribute IN (:idsList)')
            ->setParameter('idsList', $attributeIds, Connection::PARAM_INT_ARRAY)
        ;

        $result = (int) $qb->execute()->fetch()['total'];

        if (count($attributeIds) !== $result) {
            throw new AttributeNotFoundException('Some of provided attributes does not exist');
        }
    }
}
