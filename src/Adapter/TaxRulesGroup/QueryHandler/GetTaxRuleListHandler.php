<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\QueryHandler;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleList;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\TaxRuleForList;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\TaxRuleList;

/**
 * Handles query which gets paginated list of tax rules for a group
 */
#[AsQueryHandler]
final class GetTaxRuleListHandler
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function handle(GetTaxRuleList $query): TaxRuleList
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'tax_rule', 'tr')
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'country_lang',
                'cl',
                'tr.`id_country` = cl.`id_country` AND cl.`id_lang` = :idLang'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'state',
                's',
                'tr.`id_country` = s.`id_country` AND tr.`id_state` = s.`id_state`'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'tax',
                't',
                'tr.`id_tax` = t.`id_tax`'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'tax_lang',
                'tl',
                'tr.`id_tax` = tl.`id_tax` AND tl.`id_lang` = :idLang'
            )
            ->where('tr.`id_tax_rules_group` = :idTaxRulesGroup')
            ->setParameter('idLang', $query->getLanguageId())
            ->setParameter('idTaxRulesGroup', $query->getTaxRulesGroupId()->getValue())
            ->orderBy('tr.`id_tax_rule`', 'ASC')
        ;

        $countQb = clone $qb;
        $totalCount = (int) $countQb->select('COUNT(DISTINCT tr.`id_tax_rule`)')->executeQuery()->fetchOne();

        $qb->select([
            'tr.`id_tax_rule`',
            'IFNULL(cl.`name`, \'--\') AS country_name',
            'IFNULL(s.`name`, \'--\') AS state_name',
            'CASE'
                . ' WHEN CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`) = \'0 - 0\''
                . ' THEN \'--\''
                . ' ELSE CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`)'
            . ' END AS zipcode',
            'tr.`behavior`',
            'IFNULL(tl.`name`, \'\') AS tax_name',
            'IFNULL(t.`rate`, \'0\') AS tax_rate',
            'tr.`description`',
        ]);

        if ($query->getLimit() !== null) {
            $qb->setMaxResults($query->getLimit());
        }
        if ($query->getOffset() !== null) {
            $qb->setFirstResult($query->getOffset());
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $taxRules = array_map(
            static fn (array $row): TaxRuleForList => new TaxRuleForList(
                (int) $row['id_tax_rule'],
                $row['country_name'],
                $row['state_name'],
                $row['zipcode'],
                (int) $row['behavior'],
                $row['tax_name'],
                $row['tax_rate'],
                $row['description'],
            ),
            $rows
        );

        return new TaxRuleList($taxRules, $totalCount);
    }
}
