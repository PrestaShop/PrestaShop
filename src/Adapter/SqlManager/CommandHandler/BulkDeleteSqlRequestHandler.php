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
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\BulkDeleteSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShopException;
use RequestSql;

/**
 * Class BulkDeleteSqlRequestHandler handles bulk delete of SqlRequest command.
 */
#[AsCommandHandler]
final class BulkDeleteSqlRequestHandler implements BulkDeleteSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestException
     */
    public function handle(BulkDeleteSqlRequestCommand $command)
    {
        try {
            foreach ($command->getSqlRequestIds() as $sqlRequestId) {
                $entity = new RequestSql($sqlRequestId->getValue());

                if (false === $entity->delete()) {
                    throw new CannotDeleteSqlRequestException(
                        sprintf('Failed to delete SqlRequest with id %d', $sqlRequestId->getValue()),
                        CannotDeleteSqlRequestException::CANNOT_BULK_DELETE
                    );
                }
            }
        } catch (PrestaShopException $e) {
            throw new SqlRequestException('Unexpected error occurred when handling bulk delete SqlRequest', 0, $e);
        }
    }
}
