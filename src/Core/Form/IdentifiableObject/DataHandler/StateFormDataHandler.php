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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Handles submitted supplier form data
 */
class StateFormDataHandler implements FormDataHandlerInterface
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
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var StateId $stateId */
        $stateId = $this->commandBus->handle(new AddStateCommand(
            $data['id_country'],
            $data['id_zone'],
            $data['name'],
            $data['iso_code'],
            $data['active'] ?? false
        ));

        return $stateId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = new EditStateCommand((int) $id);

        $command
            ->setIsoCode($data['iso_code'])
            ->setName($data['name'])
            ->setActive($data['active'] ?? false)
            ->setCountryId($data['id_country'])
            ->setZoneId($data['id_zone']);

        $this->commandBus->handle($command);
    }
}
