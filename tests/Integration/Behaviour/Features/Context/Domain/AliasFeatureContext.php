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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteSearchTermsAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\DeleteSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasesBySearchTermForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\SearchForSearchTerm;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Query\AliasQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\DatabaseDump;

class AliasFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @BeforeFeature @restore-aliases-before-feature
     */
    public static function restoreAliasTables(): void
    {
        DatabaseDump::restoreTables(['alias']);
    }

    /**
     * @When I add a search term :searchTerm with following aliases:
     *
     * @param TableNode $table
     */
    public function addAlias(string $searchTerm, TableNode $table): void
    {
        // We retrieve the data from the table and cast the active column to a boolean
        $aliases = $table->getColumnsHash();
        array_walk($aliases, function (&$alias) {
            $alias['active'] = filter_var($alias['active'], FILTER_VALIDATE_BOOL);
        });

        // Then, we create the AddSearchTermAliasesCommand and dispatch it
        try {
            $this->getCommandBus()->handle(new AddSearchTermAliasesCommand($aliases, $searchTerm));
        } catch (InvalidArgumentException|AliasConstraintException $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When I update search term :searchTerm with following aliases:
     *
     * @param TableNode $table
     */
    public function updateSearchTermAliases(string $searchTerm, TableNode $table): void
    {
        $this->editSearchTermCommand($searchTerm, $table);
    }

    /**
     * @When I update search term :oldSearchTerm by :newSearchTerm with following aliases:
     *
     * @param TableNode $table
     */
    public function updateWithNewSearchTermAliases(string $oldSearchTerm, string $newSearchTerm, TableNode $table): void
    {
        $this->editSearchTermCommand($oldSearchTerm, $table, $newSearchTerm);
    }

    /**
     * @Then following aliases should exist:
     *
     * @param TableNode $table
     */
    public function assertAliasesList(TableNode $table): void
    {
        $expectedAliases = $table->getColumnsHash();

        /** @var AliasQueryBuilder $aliasQueryBuilder */
        $aliasQueryBuilder = $this->getContainer()->get(AliasQueryBuilder::class);
        $aliases = $aliasQueryBuilder
            ->getSearchQueryBuilder(new Filters([
                'limit' => null,
                'offset' => 0,
                'orderBy' => 'a.id_alias',
                'sortOrder' => 'asc',
                'filters' => [],
            ]))
            ->select('a.search, a.alias, a.active')
            ->executeQuery()
            ->fetchAllAssociative();

        $this->assertExistAliasProperties($expectedAliases, $aliases);
    }

    /**
     * @Then following aliases shouldn't exist:
     *
     * @param TableNode $table
     */
    public function assertAliasesNotInList(TableNode $table): void
    {
        $expectedAliases = $table->getColumnsHash();

        /** @var AliasQueryBuilder $aliasQueryBuilder */
        $aliasQueryBuilder = $this->getContainer()->get(AliasQueryBuilder::class);
        $aliases = $aliasQueryBuilder
            ->getSearchQueryBuilder(new Filters([
                'limit' => null,
                'offset' => 0,
                'orderBy' => 'a.id_alias',
                'sortOrder' => 'asc',
                'filters' => [],
            ]))
            ->select('a.search, a.alias, a.active')
            ->executeQuery()
            ->fetchAllAssociative();

        $this->assertNotExistAliasProperties($expectedAliases, $aliases);
    }

    /**
     * @Then I should have the following aliases for search term :searchTerm:
     *
     * @param string $searchTerm
     * @param TableNode $table
     */
    public function assertOneSearchTermWithAliases(string $searchTerm, TableNode $table): void
    {
        $expectedAliases = $table->getColumnsHash();

        $command = new GetAliasesBySearchTermForEditing($searchTerm);
        /** @var AliasForEditing $aliasForEditing */
        $aliasForEditing = $this->getQueryBus()->handle($command);

        $foundedAliases = $aliasForEditing->getAliases();

        /** @var array $expectedAlias */
        foreach ($expectedAliases as $index => $expectedAlias) {
            /** @var array $foundedAlias */
            $foundedAlias = $foundedAliases[$index];

            Assert::assertEquals(
                $expectedAlias['alias'],
                $foundedAlias['alias'],
                sprintf(
                    'Invalid Alias, expected %s but got %s instead.',
                    $expectedAlias['alias'],
                    $foundedAlias['alias'],
                )
            );

            Assert::assertEquals(
                filter_var($expectedAlias['active'], FILTER_VALIDATE_BOOL),
                filter_var($foundedAlias['active'], FILTER_VALIDATE_BOOL),
                sprintf(
                    'Invalid Alias Active, expected %s but got %s instead.',
                    $expectedAlias['active'],
                    $foundedAlias['active']
                )
            );
        }
    }

    /**
     * @When I search for alias search term matching :search I should get the following results:
     *
     * @param string $search
     * @param TableNode $tableNode
     */
    public function assertSearchAliases(string $search, TableNode $tableNode): void
    {
        /** @var string[] $foundAliasesForAssociation */
        $foundAliasesForAssociation = $this->getQueryBus()->handle(new SearchForSearchTerm($search));
        $expectedSearchTermsRows = $tableNode->getColumnsHash();

        foreach ($expectedSearchTermsRows as $expectedSearchTermRow) {
            $expectedSearchTerms = PrimitiveUtils::castStringArrayIntoArray($expectedSearchTermRow['searchTerm']);
            Assert::assertCount(count($expectedSearchTerms), $foundAliasesForAssociation, 'Expected and found search terms count doesn\'t match');

            foreach ($expectedSearchTerms as $index => $searchTerm) {
                $foundAliasSearchTerm = $foundAliasesForAssociation[$index];

                Assert::assertEquals(
                    $searchTerm,
                    $foundAliasSearchTerm,
                    sprintf(
                        'Invalid Alias Search Term, expected %d but got %d instead.',
                        $searchTerm,
                        $foundAliasSearchTerm
                    )
                );
            }
        }
    }

    /**
     * @Then I should get error that alias cannot be empty
     *
     * @return void
     */
    public function assertLastErrorIsInvalidAliasConstraint(): void
    {
        $this->assertLastErrorIs(AliasConstraintException::class, AliasConstraintException::INVALID_ALIAS);
    }

    /**
     * @Then I should get error that search term cannot be empty
     *
     * @return void
     */
    public function assertLastErrorIsInvalidSearchTermConstraint(): void
    {
        $this->assertLastErrorIs(AliasConstraintException::class, AliasConstraintException::INVALID_SEARCH);
    }

    /**
     * @Then I should get error that alias is already used by another search term
     *
     * @return void
     */
    public function assertLastErrorIsAliasAlreadyInUse(): void
    {
        $this->assertLastErrorIs(
            AliasConstraintException::class,
            AliasConstraintException::ALIAS_ALREADY_USED
        );
    }

    /**
     * Fonction to update search term aliases
     */
    private function editSearchTermCommand(string $oldSearchTerm, TableNode $table, ?string $newSearchTerm = null): void
    {
        // If no new searchTerm is provided, we use the old one
        $newSearchTerm = $newSearchTerm ?? $oldSearchTerm;

        // We retrieve the data from the table and cast the active column to a boolean
        $aliases = $table->getColumnsHash();
        array_walk($aliases, function (&$alias) {
            $alias['active'] = filter_var($alias['active'], FILTER_VALIDATE_BOOL);
        });

        // Then, we create the UpdateAliasesBySearchTermCommand and dispatch it
        try {
            $this->getCommandBus()->handle(new UpdateSearchTermAliasesCommand($oldSearchTerm, $aliases, $newSearchTerm));
        } catch (InvalidArgumentException|AliasConstraintException $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @param object[] $expectedData
     * @param array[] $aliases
     */
    private function assertExistAliasProperties(array $expectedData, array $aliases): void
    {
        $this->assertAliasProperties($expectedData, $aliases, true);
    }

    /**
     * @param object[] $expectedData
     * @param array[] $aliases
     */
    private function assertNotExistAliasProperties(array $expectedData, array $aliases): void
    {
        $this->assertAliasProperties($expectedData, $aliases, false);
    }

    /**
     * @param object[] $expectedData
     * @param array[] $aliases
     * @param bool $exist
     */
    private function assertAliasProperties(array $expectedData, array $aliases, bool $exist = false)
    {
        foreach ($expectedData as $key => $expectedAlias) {
            $filter = array_filter($aliases, function ($alias) use ($expectedAlias) {
                return
                    $alias['alias'] === $expectedAlias['alias']
                    && $alias['search'] === $expectedAlias['search']
                    && filter_var($alias['active'], FILTER_VALIDATE_BOOL) === filter_var($expectedAlias['active'], FILTER_VALIDATE_BOOL)
                ;
            });

            Assert::assertCount(
                $exist ? 1 : 0,
                $filter,
                sprintf(
                    "Alias '%s' for search term '%s' and active '%s' not found.",
                    $expectedAlias['alias'],
                    $expectedAlias['search'],
                    $expectedAlias['active']
                )
            );
        }
    }

    /**
     * @When I delete search term :searchTerm
     *
     * @param string $searchTerm
     */
    public function deleteSearchTerm(string $searchTerm): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteSearchTermAliasesCommand($searchTerm));
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete search terms :searchTerms
     *
     * @param string $searchTerms
     */
    public function bulkDeleteSearchTerm(string $searchTerms): void
    {
        try {
            $searchTerms = explode(',', $searchTerms);
            $this->getCommandBus()->handle(new BulkDeleteSearchTermsAliasesCommand($searchTerms));
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }
}
