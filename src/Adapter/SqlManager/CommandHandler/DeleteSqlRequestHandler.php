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

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\DeleteSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShopException;
use RequestSql;

/**
 * Class DeleteSqlRequestHandler.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteSqlRequestHandler implements DeleteSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteSqlRequestException
     * @throws SqlRequestNotFoundException
     * @throws SqlRequestException
     */
    public function handle(DeleteSqlRequestCommand $command)
    {
        $entityId = $command->getSqlRequestId()->getValue();

        try {
            $entity = new RequestSql($entityId);

            if (0 >= $entity->id) {
                throw new SqlRequestNotFoundException(sprintf('SqlRequest with id "%s" was not found for edit', var_export($entityId, true)));
            }

            if (false === $entity->delete()) {
                throw new CannotDeleteSqlRequestException(sprintf('Could not delete SqlRequest with id %s', var_export($entityId)), CannotDeleteSqlRequestException::CANNOT_SINGLE_DELETE);
            }
        } catch (PrestaShopException $e) {
            throw new SqlRequestException(sprintf('Unexpected error occurred when deleting SqlRequest with id %s', var_export($entityId, true)), 0, $e);
        }
    }
}
