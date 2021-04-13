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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use TaxRulesGroup;

/**
 * Provides access to TaxRulesGroup data source
 */
class TaxRulesGroupRepository extends AbstractObjectModelRepository
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
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @return array
     */
    public function getTaxRulesGroupDetails(TaxRulesGroupId $taxRulesGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('trg.id_tax_rules_group, trg.name, trg.active, trg.deleted, tr.id_country, t.rate')
            ->from($this->dbPrefix . 'tax_rules_group', 'trg')
            ->innerJoin('trg', $this->dbPrefix . 'tax_rule', 'tr', 'tr.id_tax_rules_group = trg.id_tax_rules_group')
            ->innerJoin('tr', $this->dbPrefix . 'tax', 't', 't.id_tax = tr.id_tax')
            ->andWhere('trg.id_tax_rules_group = :taxRulesGroupId')
            ->setParameter('taxRulesGroupId', $taxRulesGroupId->getValue())
        ;

        $rawData = $qb->execute()->fetchAll();
        if (empty($rawData)) {
            return [];
        }
        $firstRow = reset($rawData);
        $taxRulesGroup = [
            'id_tax_rules_group' => (int) $firstRow['id_tax_rules_group'],
            'name' => $firstRow['name'],
            'active' => (bool) $firstRow['active'],
            'deleted' => (bool) $firstRow['deleted'],
            'rates' => [],
        ];
        foreach ($rawData as $taxData) {
            $taxRulesGroup['rates'][(int) $taxData['id_country']] = (float) $taxData['rate'];
        }

        return $taxRulesGroup;
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @return TaxRulesGroup
     *
     * @throws CoreException
     * @throws TaxRulesGroupNotFoundException
     */
    public function get(TaxRulesGroupId $taxRulesGroupId): TaxRulesGroup
    {
        /** @var TaxRulesGroup $taxRulesGroup */
        $taxRulesGroup = $this->getObjectModel(
            $taxRulesGroupId->getValue(),
            TaxRulesGroup::class,
            TaxRulesGroupNotFoundException::class
        );

        return $taxRulesGroup;
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @throws CoreException
     * @throws TaxRulesGroupNotFoundException
     */
    public function assertTaxRulesGroupExists(TaxRulesGroupId $taxRulesGroupId): void
    {
        $this->assertObjectModelExists(
            $taxRulesGroupId->getValue(),
            'tax_rules_group',
            TaxRulesGroupNotFoundException::class
        );
    }
}
