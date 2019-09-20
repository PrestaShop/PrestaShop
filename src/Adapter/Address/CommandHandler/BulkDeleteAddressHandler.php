<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\BulkDeleteAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\BulkDeleteAddressException;

/**
 * Handles command which deletes addresses in bulk action
 */
final class BulkDeleteAddressHandler extends AbstractAddressHandler implements BulkDeleteAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteAddressException
     */
    public function handle(BulkDeleteAddressCommand $command)
    {
        $errors = [];

        foreach ($command->getAdressIds() as $addressId) {
            try {
                $address = $this->getAddress($addressId);

                if (!$this->deleteAddress($address)) {
                    $errors[] = $address->id;
                }
            } catch (AddressException $e) {
                $errors[] = $addressId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteAddressException($errors, 'Failed to delete all of selected addresses');
        }
    }
}
