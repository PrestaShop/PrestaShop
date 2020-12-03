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
     * @param CombinationDetails $expectedOptions
     */
    public function assertOptions(string $combinationReference, CombinationDetails $expectedOptions): void
    {
        $optionPropertyNames = ['ean13', 'isbn', 'mpn', 'reference', 'upc'];
        $actualOptions = $this->getCombinationForEditing($combinationReference)->getDetails();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($optionPropertyNames as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedOptions, $propertyName),
                $propertyAccessor->getValue($actualOptions, $propertyName),
                sprintf('Unexpected %s of "%s"', $propertyName, $combinationReference)
            );
        }
    }

    /**
     * @Transform table:combination detail,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationDetails
     */
    public function transformOptions(TableNode $tableNode): CombinationDetails
    {
        $options = $tableNode->getRowsHash();

        return new CombinationDetails(
            $options['ean13'],
            $options['isbn'],
            $options['mpn'],
            $options['reference'],
            $options['upc']
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
    }
}
