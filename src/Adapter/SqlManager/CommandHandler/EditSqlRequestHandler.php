<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\EditSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use RequestSql;

/**
 * Class EditSqlRequestHandler is responsible for updating RequestSql
 *
 * @internal
 */
final class EditSqlRequestHandler implements EditSqlRequestHandlerInterface
{
    /**
     * @var EditSqlRequestCommand
     */
    private $command;

    /**
     * {@inheritdoc}
     */
    public function handle(EditSqlRequestCommand $command)
    {
        $this->command = $command;

        $this->editSqlRequest();
    }

    /**
     * @throws SqlRequestException
     * @throws SqlRequestNotFoundException
     */
    private function editSqlRequest()
    {
        $entity = new RequestSql($this->command->getSqlRequestId()->getValue());

        if (0 >= $entity->id) {
            throw new SqlRequestNotFoundException(
                sprintf(
                    'RequestSql with id "%s" was not found for edit',
                    $this->command->getSqlRequestId()->getValue()
                )
            );
        }

        $entity->name = $this->command->getName();
        $entity->sql = $this->command->getSql();

        if (false === $entity->update()) {
            throw new SqlRequestException(
                sprintf(
                    'Error occurred when updating RequestSql with id "%s"',
                    $this->command->getSqlRequestId()->getValue()
                )
            );
        }
    }
}
