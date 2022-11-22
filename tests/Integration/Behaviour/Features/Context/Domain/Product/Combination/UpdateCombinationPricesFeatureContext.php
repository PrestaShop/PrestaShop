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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationPricesCommand;

class UpdateCombinationPricesFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update combination :combinationReference prices with following details:
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updatePrices(string $combinationReference, TableNode $tableNode): void
    {
        $command = new UpdateCombinationPricesCommand($this->getSharedStorage()->get($combinationReference));
        $this->fillCommand($command, $tableNode->getRowsHash());

        $this->getCommandBus()->handle($command);
    }

    /**
     * @param UpdateCombinationPricesCommand $command
     * @param array<string, string> $data
     */
    private function fillCommand(UpdateCombinationPricesCommand $command, array $data): void
    {
        if (isset($data['eco tax'])) {
            $command->setEcoTax($data['eco tax']);
        }
        if (isset($data['impact on price'])) {
            $command->setImpactOnPrice($data['impact on price']);
        }
        if (isset($data['impact on unit price'])) {
            $command->setImpactOnUnitPrice($data['impact on unit price']);
        }
        if (isset($data['wholesale price'])) {
            $command->setWholesalePrice($data['wholesale price']);
        }
    }
}
