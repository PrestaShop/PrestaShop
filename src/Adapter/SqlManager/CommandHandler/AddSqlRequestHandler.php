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

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\AddSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use PrestaShopException;
use RequestSql;

/**
 * Class AddSqlRequestHandler handles SqlRequest creation command.
 *
 * @internal
 */
final class AddSqlRequestHandler implements AddSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddSqlRequestException
     * @throws SqlRequestException
     */
    public function handle(AddSqlRequestCommand $command)
    {
        try {
            $entity = new RequestSql();
            $entity->name = $command->getName();
            $entity->sql = $command->getSql();

            $entity->add();

            if (0 >= $entity->id) {
                throw new CannotAddSqlRequestException(
                    sprintf('Invalid entity id after creation: %s', $entity->id)
                );
            }

            return new SqlRequestId($entity->id);
        } catch (PrestaShopException $e) {
            throw new SqlRequestException(
                'Failed to create SqlRequest',
                0,
                $e
            );
        }
    }
}
