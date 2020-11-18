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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\DeleteManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;

/**
 * Handles command which deletes manufacturer using legacy object model
 */
final class DeleteManufacturerHandler extends AbstractManufacturerCommandHandler implements DeleteManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteManufacturerCommand $command)
    {
        $manufacturer = $this->getManufacturer($command->getManufacturerId());

        if (!$this->deleteManufacturer($manufacturer)) {
            throw new DeleteManufacturerException(sprintf(
                'Cannot delete Manufacturer object with id "%s".', $manufacturer->id),
                DeleteManufacturerException::FAILED_DELETE
            );
        }
    }
}
