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
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use RuntimeException;
use Tax;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

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
    public function createTaxUsingCommand($taxReference, TableNode $table)
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
     * @When I edit tax :taxReference with following properties:
     */
    public function editTaxUsingCommand($taxReference, TableNode $table)
    {
        $defaultLang = Configuration::get('PS_LANG_DEFAULT');

        $data = $table->getRowsHash();
        $commandBus = $this->getCommandBus();

        /** @var Tax $tax */
        $tax = SharedStorage::getStorage()->get($taxReference);
        $taxId = (int) $tax->id;
        $command = new EditTaxCommand($taxId);
        $command->setLocalizedNames([$defaultLang => $data['name']]);
        $command->setRate($data['rate']);

        $commandBus->handle($command);

        SharedStorage::getStorage()->set($taxReference, new Tax($taxId));
    }

    /**
     * @When I delete tax with id :id
     */
    public function deleteTaxUsingCommand($id)
    {
        $commandBus = $this->getCommandBus();
        $command = new DeleteTaxCommand((int) $id);

        $commandBus->handle($command);
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

    /**
     * @Then Tax with id :id should not exist
     */
    public function assertTaxByIdShouldNotExist($id)
    {
        try {
            $query = new GetTaxForEditing((int) $id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException();
        } catch (TaxNotFoundException $e) {
        }
    }

    /**
     * @Given Tax with id :id exists
     */
    public function assertTaxExistsById($id)
    {
        $query = new GetTaxForEditing((int) $id);

        $this->getQueryBus()->handle($query);
    }
}
