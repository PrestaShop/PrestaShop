<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use RuntimeException;
use Tax;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class TaxFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /**
     * @When I add new tax :taxReference with following properties:
     */
    public function createATaxUsingCommand($taxReference, TableNode $table)
    {
        $defaultLang = Configuration::get('PS_LANG_DEFAULT');

        $data = $table->getRowsHash();
        $commandBus = $this->getCommandBus();
        $mandatoryFields = [
            'name',
            'rate',
        ];

        foreach ($mandatoryFields as $mandatoryField) {
            if (!array_key_exists($mandatoryField, $data)) {
                throw new \Exception(sprintf('Mandatory property %s for tax has not been provided', $mandatoryField));
            }
        }

        $command = new AddTaxCommand(
           [$defaultLang => $data['name']],
            $data['rate'],
            isset($data['is_enabled']) ? $data['is_enabled'] : null
        );

        /** @var TaxId $taxId */
        $taxId = $commandBus->handle($command);

        SharedStorage::getStorage()->set($taxReference, new Tax($taxId->getValue()));
    }

    /**
     * @Then tax :taxReference name in default language should be :name
     */
    public function assertTaxNameInDefaultLang($taxReference, $name)
    {
        $defaultLang = Configuration::get('PS_LANG_DEFAULT');

        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);

        if ($tax->name[$defaultLang] !== $name) {
            throw new RuntimeException(sprintf(
                'Tax "%s" has "%s" name, but "%s" was expected.',
                $taxReference,
                $tax->name,
                $name
            ));
        }
    }

    /**
     * @Then tax :taxReference rate should be :rate
     */
    public function assertTaxRate($taxReference, $rate)
    {
        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);

        if ($tax->rate !== $rate) {
            throw new RuntimeException(sprintf(
                'Tax "%s" has "%s" rate, but "%s" was expected.',
                $taxReference,
                $tax->rate,
                $rate
            ));
        }
    }
}
