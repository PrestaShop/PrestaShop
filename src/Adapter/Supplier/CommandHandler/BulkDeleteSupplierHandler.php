<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkDeleteSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class BulkDeleteSupplierHandler is responsible for deleting multiple suppliers.
 */
final class BulkDeleteSupplierHandler implements BulkDeleteSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkDeleteSupplierCommand $command)
    {
        try {
            foreach ($command->getSupplierIds() as $supplierId) {
                $entity = new Supplier($supplierId->getValue());

                if (0 >= $entity->id) {
                    throw new SupplierNotFoundException(
                        sprintf(
                            'Supplier object with id "%s" has not been found for deletion.',
                            $supplierId->getValue()
                        )
                    );
                }

                if (false === $entity->delete()) {
                    throw new CannotDeleteSupplierException(
                        $supplierId,
                        sprintf(
                            'Unable to delete supplier object with id "%s"',
                            $supplierId->getValue()
                        )
                    );
                }
            }
        } catch (PrestaShopException $exception) {
            throw new SupplierException(
                'Unexpected error occurred when handling bulk delete supplier',
                0,
                $exception
            );
        }
    }
}
