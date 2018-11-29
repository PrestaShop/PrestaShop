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

use PrestaShop\PrestaShop\Adapter\Supplier\SupplierOrderValidator;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\DeleteSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class DeleteSupplierHandler is responsible for deleting the supplier.
 */
final class DeleteSupplierHandler implements DeleteSupplierHandlerInterface
{
    /**
     * @var SupplierOrderValidator
     */
    private $supplierOrderValidator;

    /**
     * @param SupplierOrderValidator $supplierOrderValidator
     */
    public function __construct(SupplierOrderValidator $supplierOrderValidator)
    {
        $this->supplierOrderValidator = $supplierOrderValidator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(DeleteSupplierCommand $command)
    {
        try {
            $entity = new Supplier($command->getSupplierId()->getValue());

            if (0 >= $entity->id) {
                throw new SupplierNotFoundException(
                    sprintf(
                        'Supplier object with id "%s" has not been found for deletion.',
                        $command->getSupplierId()->getValue()
                    )
                );
            }

            if ($this->supplierOrderValidator->hasPendingOrders($command->getSupplierId()->getValue())) {
                throw new CannotDeleteSupplierException(
                    $command->getSupplierId(),
                    sprintf(
                        'Supplier with id %s cannot be deleted due to it has pending orders',
                        $command->getSupplierId()->getValue()
                    ),
                    CannotDeleteSupplierException::HAS_PENDING_ORDERS
                );
            }

            if (false === $entity->delete()) {
                throw new CannotDeleteSupplierException(
                    $command->getSupplierId(),
                    sprintf(
                        'Unable to delete supplier object with id "%s"',
                        $command->getSupplierId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new SupplierException(
                sprintf(
                    'An error occurred when deleting the supplier object with id "%s"',
                    $command->getSupplierId()->getValue()
                ),
                0,
                $exception
            );
        }
    }
}
