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
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
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
     * @param CountryId $countryId
     *
     * @return int
     */
    public function getTaxRulesGroupDefaultStateId(TaxRulesGroupId $taxRulesGroupId, CountryId $countryId): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('tr.id_state')
            ->from($this->dbPrefix . 'tax_rules_group', 'trg')
            ->innerJoin('trg', $this->dbPrefix . 'tax_rule', 'tr', 'tr.id_tax_rules_group = trg.id_tax_rules_group')
            ->innerJoin('tr', $this->dbPrefix . 'tax', 't', 't.id_tax = tr.id_tax')
            ->andWhere('trg.id_tax_rules_group = :taxRulesGroupId')
            ->andWhere('tr.id_country = :countryId')
            ->andWhere('trg.deleted = 0')
            ->setParameters([
                'taxRulesGroupId' => $taxRulesGroupId->getValue(),
                'countryId' => $countryId->getValue(),
            ])
        ;

        $rawData = $qb->execute()->fetchAll();
        if (empty($rawData)) {
            return 0;
        }
        $firstRow = reset($rawData);

        return (int) $firstRow['id_state'];
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
