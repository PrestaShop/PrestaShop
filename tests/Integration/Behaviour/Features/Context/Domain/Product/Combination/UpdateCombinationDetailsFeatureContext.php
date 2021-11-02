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
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UpdateCombinationDetailsFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update combination :combinationReference details with following values:
     *
     * @param string $combinationReference
     * @param TableNode $tableNode
     */
    public function updateDetails(string $combinationReference, TableNode $tableNode): void
    {
        $command = new UpdateCombinationDetailsCommand($this->getSharedStorage()->get($combinationReference));

        $this->fillCommand($command, $tableNode->getRowsHash());
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then combination :combinationReference should have following details:
     *
     * @param string $combinationReference
     * @param CombinationDetails $expectedDetails
     */
    public function assertDetails(string $combinationReference, CombinationDetails $expectedDetails): void
    {
        $scalarDetailNames = ['ean13', 'isbn', 'mpn', 'reference', 'upc'];
        $actualDetails = $this->getCombinationForEditing($combinationReference)->getDetails();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($scalarDetailNames as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedDetails, $propertyName),
                $propertyAccessor->getValue($actualDetails, $propertyName),
                sprintf('Unexpected %s of "%s"', $propertyName, $combinationReference)
            );
        }

        Assert::assertTrue(
            $expectedDetails->getImpactOnWeight()->equals($actualDetails->getImpactOnWeight()),
            sprintf(
                'Unexpected combination impact on weight. Expected "%s" got "%s"',
                var_export($expectedDetails, true),
                var_export($actualDetails, true)
            )
        );
    }

    /**
     * @Transform table:combination detail,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationDetails
     */
    public function transformDetails(TableNode $tableNode): CombinationDetails
    {
        $details = $tableNode->getRowsHash();

        return new CombinationDetails(
            $details['ean13'],
            $details['isbn'],
            $details['mpn'],
            $details['reference'],
            $details['upc'],
            new DecimalNumber($details['impact on weight'] ?? '0')
        );
    }

    /**
     * @param UpdateCombinationDetailsCommand $command
     * @param array $dataRows
     */
    private function fillCommand(UpdateCombinationDetailsCommand $command, array $dataRows): void
    {
        if (isset($dataRows['ean13'])) {
            $command->setEan13($dataRows['ean13']);
        }
        if (isset($dataRows['isbn'])) {
            $command->setIsbn($dataRows['isbn']);
        }
        if (isset($dataRows['mpn'])) {
            $command->setMpn($dataRows['mpn']);
        }
        if (isset($dataRows['reference'])) {
            $command->setReference($dataRows['reference']);
        }
        if (isset($dataRows['upc'])) {
            $command->setUpc($dataRows['upc']);
        }
        if (isset($dataRows['impact on weight'])) {
            $command->setWeight($dataRows['impact on weight']);
        }
    }
}
