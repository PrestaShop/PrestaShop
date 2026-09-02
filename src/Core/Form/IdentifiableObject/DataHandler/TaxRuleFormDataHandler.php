<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\EditTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult\AddTaxRuleResult;

/**
 * Handles submitted tax rule form data
 */
class TaxRuleFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = new AddTaxRuleCommand(
            (int) $data['tax_rules_group_id'],
            (int) $data['country'],
            (int) $data['tax']
        );

        if (!empty($data['state'])) {
            $stateIds = is_array($data['state']) ? array_map('intval', $data['state']) : [(int) $data['state']];
            $command->setStateIds($stateIds);
        }

        if (array_key_exists('zipcode', $data)) {
            $command->setZipCode((string) ($data['zipcode'] ?? ''));
        }

        if (array_key_exists('behavior', $data)) {
            $command->setBehavior((int) $data['behavior']);
        }

        if (array_key_exists('description', $data)) {
            $command->setDescription((string) ($data['description'] ?? ''));
        }

        /** @var AddTaxRuleResult $result */
        $result = $this->commandBus->handle($command);

        // Return the (potentially new) group id after historization
        return $result->getTaxRulesGroupId()->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $command = new EditTaxRuleCommand((int) $id);

        if (array_key_exists('country', $data)) {
            $command->setCountryId((int) $data['country']);
        }
        if (array_key_exists('state', $data)) {
            $command->setStateId((int) ($data['state'] ?? 0));
        }
        if (array_key_exists('zipcode', $data)) {
            $command->setZipCode((string) ($data['zipcode'] ?? ''));
        }
        if (array_key_exists('behavior', $data)) {
            $command->setBehavior((int) $data['behavior']);
        }
        if (array_key_exists('tax', $data)) {
            $command->setTaxId((int) $data['tax']);
        }
        if (array_key_exists('description', $data)) {
            $command->setDescription((string) ($data['description'] ?? ''));
        }

        $this->commandBus->handle($command);
    }
}
