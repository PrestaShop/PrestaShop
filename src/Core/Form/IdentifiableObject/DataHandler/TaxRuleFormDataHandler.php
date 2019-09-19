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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\UpdateTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;

/**
 * Handles submitted tax rule form data
 */
final class TaxRuleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Create object from form data.
     *
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxConstraintException
     * @throws TaxRuleConstraintException
     * @throws TaxRulesGroupConstraintException
     */
    public function create(array $data)
    {
        $states = !empty($data['state']) ? $data['state'] : [0];

        $command = new AddTaxRuleCommand(
            (int) $data['taxRulesGroupId'],
            (int) $data['behavior'],
            array_map('intval', $states)
        );

        if (!empty($data['tax'])) {
            $command->setTaxId((int) $data['tax']);
        }

        if (!empty($data['description'])) {
            $command->setDescription($data['description']);
        }

        if (!empty($data['country'])) {
            $command->setCountryId((int) $data['country']);
        }

        if (!empty($data['zipCode'])) {
            $command->setZipCode($data['zipCode']);
        }

        /** @var TaxRuleId $taxRuleId */
        $taxRuleId = $this->commandBus->handle($command);

        //TODO find out how to handle hooks with multiple tax rule creation
        return $taxRuleId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxConstraintException
     * @throws TaxRuleConstraintException
     */
    public function update($id, array $data)
    {
        $states = !empty($data['state']) ? $data['state'] : [0];

        $command = new UpdateTaxRuleCommand(
            $id,
            (int) $data['country'],
            (int) $data['behavior'],
            array_map('intval', $states)
        );

        if (null !== $data['description']) {
            $command->setDescription($data['description']);
        }

        if (!empty($data['tax'])) {
            $command->setTaxId($data['tax']);
        }

        if (!empty($data['zipCode'])) {
            $command->setZipCode($data['zipCode']);
        }

        $this->commandBus->handle($command);
    }
}
