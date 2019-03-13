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
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Handles submitted manufacturer address form data
 */
final class ManufacturerAddressFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc]
     */
    public function create(array $data)
    {
        /**
         * @var AddressId
         */
        $addressId = $this->commandBus->handle(new AddManufacturerAddressCommand(
            $data['id_manufacturer'],
            $data['last_name'],
            $data['first_name'],
            $data['address'],
            $data['id_country'],
            $data['city'],
            $data['address2'],
            $data['post_code'],
            $data['id_state'],
            $data['home_phone'],
            $data['mobile_phone'],
            $data['other']
        ));

        return $addressId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($addressId, array $data)
    {
        $command = new EditManufacturerAddressCommand((int) $addressId);
        $addressId = $this->commandBus->handle($command);
    }
}
