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

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkDisableSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class BulkDisableSupplierHandler is responsible for disabling multiple suppliers.
 */
final class BulkDisableSupplierHandler implements BulkDisableSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkDisableSupplierCommand $command)
    {
        try {
            foreach ($command->getSupplierIds() as $supplierId) {
                $entity = new Supplier($supplierId->getValue());

                if (0 >= $entity->id) {
                    throw new SupplierNotFoundException(
                        sprintf(
                            'Supplier object with id "%s" has not been found for disabling status.',
                            $supplierId->getValue()
                        )
                    );
                }

                $entity->active = false;

                if (false === $entity->update()) {
                    throw new CannotUpdateSupplierStatusException(
                        sprintf(
                            'Unable to disable supplier object with id "%s"',
                            $supplierId->getValue()
                        )
                    );
                }
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException(
                'Unexpected error occurred when handling bulk disable supplier',
                0,
                $e
            );
        }
    }
}
