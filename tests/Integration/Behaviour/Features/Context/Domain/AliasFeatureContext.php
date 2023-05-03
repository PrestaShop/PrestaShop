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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Driver\Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddAliasCommand;
use PrestaShop\PrestaShop\Core\Grid\Query\AliasQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\AliasFilters;
use Tests\Resources\DatabaseDump;

class AliasFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add alias with following information:
     *
     * @param TableNode $table
     */
    public function addAlias(TableNode $table): void
    {
        $data = $table->getRowsHash();

        $aliases = array_map('trim', explode(',', $data['alias']));

        $this->getCommandBus()->handle(new AddAliasCommand(
            $aliases,
            $data['search']
        ));
    }

    /**
     * @Then following aliases should exist:
     *
     * @param TableNode $table
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function assertAlias(TableNode $table): void
    {
        $data = $table->getColumnsHash();

        /** @var AliasQueryBuilder $aliasQueryBuilder */
        $aliasQueryBuilder = $this->getContainer()->get(AliasQueryBuilder::class);
        $qb = $aliasQueryBuilder->getSearchQueryBuilder(AliasFilters::buildDefaults());
        $aliases = $qb->execute()->fetchAllAssociative();

        Assert::assertEquals(
            count($data),
            count($aliases),
            'Unexpected aliases count'
        );

        $idsByIdReferences = $this->assertAliasProperties($data, $aliases);

        foreach ($idsByIdReferences as $reference => $id) {
            $this->getSharedStorage()->set($reference, $id);
        }
    }

    /**
     * @BeforeFeature @restore-aliases-before-feature
     */
    public static function restoreAliasTablesBeforeFeature(): void
    {
        DatabaseDump::restoreTables(['alias']);
    }

    /**
     * @param array $expectedData
     * @param array $aliases
     *
     * @return array
     */
    private function assertAliasProperties(array $expectedData, array $aliases): array
    {
        $idsByIdReferences = [];
        foreach ($aliases as $key => $alias) {
            $expectedAlias = $expectedData[$key];

            Assert::assertSame(
                $alias['alias'],
                $expectedAlias['alias'],
                'Unexpected alias reference'
            );

            Assert::assertSame(
                $alias['search'],
                $expectedAlias['search'],
                'Unexpected alias reference'
            );

            if (!empty($expectedAlias['id reference'])) {
                $idsByIdReferences[$expectedAlias['id reference']] = $alias['id_alias'];
            }
        }

        return $idsByIdReferences;
    }
}
