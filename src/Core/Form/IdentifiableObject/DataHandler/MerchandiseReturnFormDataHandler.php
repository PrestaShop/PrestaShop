<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Command\EditMerchandiseReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnStateId;

/**
 * Saves or updates customer data submitted in form
 */
final class MerchandiseReturnFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function update($merchandiseReturnId, array $data): void
    {
        $merchandiseReturnId = new MerchandiseReturnId($merchandiseReturnId);
        $command = $this->buildMerchandiseReturnEditCommand($merchandiseReturnId, $data);

        $this->bus->handle($command);
    }

    /**
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param array $data
     *
     * @return EditMerchandiseReturnCommand
     * @throws \PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnOrderStateConstraintException
     */
    private function buildMerchandiseReturnEditCommand(MerchandiseReturnId $merchandiseReturnId, array $data): EditMerchandiseReturnCommand
    {
        $merchandiseReturnStateId = new MerchandiseReturnStateId((int) $data['merchandise_return_order_state']);
        $command = (new EditMerchandiseReturnCommand($merchandiseReturnId))
          ->setMerchandiseReturnStateId($merchandiseReturnStateId)
        ;

        return $command;
    }

    /**
     * Merchandise Return don't have a create option
     *
     * @param array $data
     *
     * @return mixed ID of identifiable object
     */
    public function create(array $data): void
    {
    }
}
