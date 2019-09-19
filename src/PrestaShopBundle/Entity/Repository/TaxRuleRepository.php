<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;

/**
 * Repository provides common methods for tax rule DB interaction
 */
class TaxRuleRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $databasePrefix;

    /**
     * @var string
     */
    private $table;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->table = $this->databasePrefix . 'tax_rule';
    }

    /**
     * @param int $taxRuleGroupId
     * @param int $countryId
     * @param int $stateId
     * @param int|null $taxRuleId
     * @param int $behaviorId
     *
     * @return bool
     */
    public function hasUniqueBehaviorTaxRule(
        int $taxRuleGroupId,
        int $countryId,
        int $stateId,
        int $taxRuleId,
        int $behaviorId = 0
    ): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('tr.id_tax_rule')
            ->from($this->table, 'tr')
            ->where('tr.id_tax_rules_group = :taxRulesGroupId')
            ->andWhere('tr.id_country = :countryId')
            ->andWhere('tr.id_state = :stateId')
            ->andWhere('tr.id_tax_rule <> :taxRuleId')
            ->andWhere('tr.behavior = :behavior')
            ->setParameter('taxRulesGroupId', $taxRuleGroupId)
            ->setParameter('countryId', $countryId)
            ->setParameter('stateId', $stateId)
            ->setParameter('taxRuleId', $taxRuleId)
            ->setParameter('behavior', $behaviorId)
            ->setMaxResults(1);

        return !$qb->execute()->fetchColumn() ? false : true;
    }
}
