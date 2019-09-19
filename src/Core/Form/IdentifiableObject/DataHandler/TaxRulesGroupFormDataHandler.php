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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\UpdateTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles submitted tax rules group form data
 */
final class TaxRulesGroupFormDataHandler implements FormDataHandlerInterface
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
     */
    public function create(array $data)
    {
        $command = new AddTaxRulesGroupCommand(
            $data['name'],
            $data['enabled'] ?? false
        );

        if (null !== $data['shop_association'] && !empty($data['shop_association'])) {
            $command->setShopAssociation($data['shop_association']);
        }

        /** @var TaxRulesGroupId $taxRulesGroupId */
        $taxRulesGroupId = $this->commandBus->handle($command);

        return $taxRulesGroupId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function update($id, array $data)
    {
        $command = new UpdateTaxRulesGroupCommand($id);

        if (null !== $data['name']) {
            $command->setName($data['name']);
        }

        if (null !== $data['enabled']) {
            $command->setEnabled($data['enabled']);
        }

        if (null !== $data['shop_association'] && !empty($data['shop_association'])) {
            $command->setShopAssociation($data['shop_association']);
        }

        $this->commandBus->handle($command);
    }
}
