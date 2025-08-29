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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkEnableSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class BulkEnableSupplierHandler is responsible for enabling multiple suppliers.
 */
#[AsCommandHandler]
final class BulkEnableSupplierHandler implements BulkEnableSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkEnableSupplierCommand $command)
    {
        try {
            foreach ($command->getSupplierIds() as $supplierId) {
                $entity = new Supplier($supplierId->getValue());

                if (0 >= $entity->id) {
                    throw new SupplierNotFoundException(sprintf('Supplier object with id "%s" has not been found for enabling status.', $supplierId->getValue()));
                }

                $entity->active = true;

                if (false === $entity->update()) {
                    throw new CannotUpdateSupplierStatusException(sprintf('Unable to enable supplier object with id "%s"', $supplierId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException('Unexpected error occurred when handling bulk enable supplier', 0, $e);
        }
    }
}
