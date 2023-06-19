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
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddAliasCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteAliasCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkUpdateAliasStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\DeleteAliasCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateAliasCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateAliasStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Query\AliasQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
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

        try {
            $this->getCommandBus()->handle(new AddAliasCommand(
                PrimitiveUtils::castStringArrayIntoArray($data['alias']),
                $data['search']
            ));
        } catch (InvalidArgumentException $exception) {
            $this->setLastException($exception);
        }
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
                'orderBy' => 'id_alias',
                'sortOrder' => 'asc',
                'filters' => [],
            ]))
            ->execute()
            ->fetchAllAssociative();

        Assert::assertCount(count($expectedAliases), $aliases, 'Unexpected aliases count');

        $idsByIdReferences = $this->assertAliasProperties($expectedAliases, $aliases);

        foreach ($idsByIdReferences as $reference => $id) {
            $this->getSharedStorage()->set($reference, (int) $id);
        }
    }

    /**
     * @When /^I (enable|disable) alias with reference "(.+)"$/
     *
     * @param bool $enable
     * @param string $aliasReference
     *
     * @see StringToBoolTransformContext::transformTruthyStringToBoolean for $enable string to bool transformation
     */
    public function updateAliasStatus(bool $enable, string $aliasReference): void
    {
        $this->getCommandBus()->handle(new UpdateAliasStatusCommand(
            new AliasId((int) $this->getSharedStorage()->get($aliasReference)),
            $enable
        ));
    }

    /**
     * @When /^I bulk (enable|disable) the following aliases "([^"]*)"$/
     *
     * @param bool $status
     * @param string $aliasReferences
     */
    public function bulkUpdateStatusForDefaultShop(bool $status, string $aliasReferences): void
    {
        try {
            $this->getCommandBus()->handle(new BulkUpdateAliasStatusCommand($this->referencesToIds($aliasReferences), $status));
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that alias cannot be empty
     * @Then I should get error that search term cannot be empty
     *
     * @return void
     */
    public function assertLastErrorIsInvalidAliasConstraint(): void
    {
        $this->assertLastErrorIs(InvalidArgumentException::class);
    }

    /**
     * @When I update alias ":aliasReference" with following values:
     *
     * @return void
     */
    public function assertUpdateAlias(string $aliasReference, TableNode $table): void
    {
        $updateAliasCommand = $this->buildUpdateAliasCommand($aliasReference, $table);

        try {
            $this->getCommandBus()->handle($updateAliasCommand);
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete alias :reference
     *
     * @param string $reference
     */
    public function deleteAlias(string $reference): void
    {
        /** @var string[] $aliasId */
        $aliasId = $this->getSharedStorage()->get($reference);

        try {
            $this->getCommandBus()->handle(new DeleteAliasCommand(new AliasId((int) $aliasId)));
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete aliases :aliasReferences
     */
    public function bulkDeleteAlias(string $aliasReferences): void
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteAliasCommand($this->referencesToIds($aliasReferences)));
        } catch (AliasException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @BeforeFeature @restore-aliases-before-feature
     */
    public static function restoreAliasTables(): void
    {
        DatabaseDump::restoreTables(['alias']);
    }

    /**
     * @param object[] $expectedData
     * @param array[] $aliases
     *
     * @return string[]
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

            if (isset($expectedAlias['active'])) {
                Assert::assertSame(
                    (int) $alias['active'],
                    (int) $expectedAlias['active'],
                    'Unexpected alias active field'
                );
            }

            if (isset($expectedAlias['id reference'])) {
                $idsByIdReferences[$expectedAlias['id reference']] = $alias['id_alias'];
            }
        }

        return $idsByIdReferences;
    }

    private function buildUpdateAliasCommand(string $aliasReference, TableNode $table): UpdateAliasCommand
    {
        $data = $table->getRowsHash();
        $aliasId = $this->getSharedStorage()->get($aliasReference);
        $aliases = [];
        $searchTerm = '';

        if (isset($data['aliases'])) {
            $aliases = PrimitiveUtils::castStringArrayIntoArray($data['aliases']);
        }
        if (isset($data['search'])) {
            $searchTerm = $data['search'];
        }

        return new UpdateAliasCommand($aliasId, $aliases, $searchTerm);
    }
}
