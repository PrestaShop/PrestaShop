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

use Exception;
use PrestaShop\PrestaShop\Adapter\SqlManager\SqlQueryValidator;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\AddSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use RequestSql;

/**
 * Class AddSqlRequestHandler handles RequestSql creation command
 */
final class AddSqlRequestHandler implements AddSqlRequestHandlerInterface
{
    /**
     * @var AddSqlRequestCommand
     */
    private $command;
    /**
     * @var SqlQueryValidator
     */
    private $sqlQueryValidator;

    /**
     * @param SqlQueryValidator $sqlQueryValidator
     */
    public function __construct(SqlQueryValidator $sqlQueryValidator)
    {
        $this->sqlQueryValidator = $sqlQueryValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddSqlRequestCommand $command)
    {
        $this->command = $command;

        return $this->buildRequestSql();
    }

    /**
     * @return SqlRequestId
     *
     * @throws CannotAddSqlRequestException
     * @throws SqlRequestConstraintException
     * @throws SqlRequestException
     */
    private function buildRequestSql()
    {
        $this->validateSqlQuery();

        try {
            $entity = new RequestSql();
            $entity->name = $this->command->getName();
            $entity->sql = $this->command->getSql();

            $entity->add();

            if (0 >= $entity->id) {
                throw new SqlRequestException(
                    sprintf('Invalid entity id after creation: %s', $entity->id)
                );
            }
        } catch (Exception $e) {
            throw new CannotAddSqlRequestException(
                'Failed to create RequestSql',
                0,
                $e
            );
        }

        return new SqlRequestId($entity->id);
    }

    /**
     * Check if SQL query is valid
     *
     * @throws SqlRequestConstraintException
     */
    private function validateSqlQuery()
    {
        if (!empty($errors = $this->sqlQueryValidator->validate($this->command->getSql()))) {
            throw new SqlRequestConstraintException(
                sprintf('RequestSql query "%s" is malformed', $this->command->getSql()),
                SqlRequestConstraintException::MALFORMED_SQL_QUERY_ERROR
            );
        }
    }
}
