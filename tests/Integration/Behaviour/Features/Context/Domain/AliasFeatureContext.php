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
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Resources\DatabaseDump;

class AliasFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add alias :reference with following information:
     *
     * @param string $reference
     * @param TableNode $table
     */
    public function addAlias(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $aliases = array_map('trim', explode(',', $data['alias']));

        /** @var AliasId[] $aliasIds */
        $aliasIds = $this->getCommandBus()->handle(new AddAliasCommand(
            $aliases,
            $data['search']
        ));

        $this->getSharedStorage()->set($reference, $aliasIds);
    }

    /**
     * @Then alias :reference should have the following details:
     *
     * @param string $reference
     * @param TableNode $table
     */
    public function assertAlias(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $expectedEditableContacts = $this->mapToEditableAlias($data);

        /** @var AliasId[] $aliasIds */
        $aliasIds = SharedStorage::getStorage()->get($reference);

        foreach ($aliasIds as $aliasId) {
            /** @var AliasForEditing $aliasForEditing */
            $aliasForEditing = $this->getQueryBus()->handle(new GetAliasForEditing($aliasId->getValue()));

            Assert::assertEquals($aliasForEditing, $expectedEditableContacts);
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
     * @param array $data
     *
     * @return AliasForEditing
     */
    private function mapToEditableAlias(array $data): AliasForEditing
    {
        return new AliasForEditing(
            explode(',', $data['alias']),
            $data['search']
        );
    }
}
