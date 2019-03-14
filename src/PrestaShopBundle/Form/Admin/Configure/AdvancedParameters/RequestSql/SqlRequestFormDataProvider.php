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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotEditSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class RequestSqlFormDataProvider is responsible for getting/saving RequestSql form data.
 */
class SqlRequestFormDataProvider
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $commandBus
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $commandBus,
        CommandBusInterface $queryBus
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * Get RequestSql form data.
     *
     * @param int $requestSqlId
     *
     * @return array
     */
    public function getData($requestSqlId)
    {
        try {
            $getRequestSqlForEditingQuery = new GetSqlRequestForEditing($requestSqlId);

            /** @var EditableSqlRequest $editableRequestSql */
            $editableRequestSql = $this->queryBus->handle($getRequestSqlForEditingQuery);

            return [
                'id' => $editableRequestSql->getSqlRequestId()->getValue(),
                'name' => $editableRequestSql->getName(),
                'sql' => $editableRequestSql->getSql(),
            ];
        } catch (SqlRequestException $e) {
            return [];
        }
    }

    /**
     * Save form data for RequestSql.
     *
     * @param array $requestSqlData
     *
     * @return array Array of errors if any
     */
    public function saveData(array $requestSqlData)
    {
        $errors = [];

        try {
            $command = isset($requestSqlData['id']) ?
                $this->getEditRequestSqlCommand($requestSqlData) :
                $this->getAddRequestSqlCommand($requestSqlData);

            $this->commandBus->handle($command);
        } catch (SqlRequestException $e) {
            $errors[] = $this->handleException($e);
        }

        return $errors;
    }

    /**
     * @param array $requestSqlData
     *
     * @return AddSqlRequestCommand
     *
     * @throws SqlRequestConstraintException
     */
    private function getAddRequestSqlCommand(array $requestSqlData)
    {
        return new AddSqlRequestCommand(
            $requestSqlData['name'],
            $requestSqlData['sql']
        );
    }

    /**
     * @param array $requestSqlData
     *
     * @return EditSqlRequestCommand
     *
     * @throws SqlRequestException
     */
    private function getEditRequestSqlCommand(array $requestSqlData)
    {
        return (new EditSqlRequestCommand(new SqlRequestId($requestSqlData['id'])))
            ->setName($requestSqlData['name'])
            ->setSql($requestSqlData['sql']);
    }

    /**
     * Transform exception into translatable errors.
     *
     * @param SqlRequestException $e
     *
     * @return array Errors
     */
    private function handleException(SqlRequestException $e)
    {
        $exceptionType = get_class($e);

        if (SqlRequestConstraintException::class === $exceptionType) {
            return $this->getConstraintError($e);
        }

        return $this->getErrorByExceptionType($e);
    }

    /**
     * Get error for constraint exception.
     *
     * @param SqlRequestConstraintException $e
     *
     * @return array
     */
    private function getConstraintError(SqlRequestConstraintException $e)
    {
        $invalidFieldDictionary = [
            SqlRequestConstraintException::INVALID_NAME => 'name',
            SqlRequestConstraintException::INVALID_SQL_QUERY => 'sql',
            SqlRequestConstraintException::MALFORMED_SQL_QUERY => 'sql',
        ];

        $code = $e->getCode();

        if (isset($invalidFieldDictionary[$code])) {
            return [
                'key' => 'The %s field is invalid.',
                'parameters' => [
                    $invalidFieldDictionary[$code],
                ],
                'domain' => 'Admin.Notifications.Error',
            ];
        }

        return [
            'key' => 'Invalid data supplied.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get error for exception.
     *
     * @param SqlRequestException $e
     *
     * @return array
     */
    private function getErrorByExceptionType(SqlRequestException $e)
    {
        $exceptionDictionary = [
            SqlRequestNotFoundException::class => [
                'key' => 'The object cannot be loaded (or found)',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
            CannotAddSqlRequestException::class => [
                'key' => 'An error occurred while creating an object.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
            CannotEditSqlRequestException::class => [
                'key' => 'An error occurred while updating an object.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
        ];

        $exceptionType = get_class($e);

        if (isset($exceptionDictionary[$exceptionType])) {
            return $exceptionDictionary[$exceptionType];
        }

        return [
            'key' => 'Unexpected error occurred.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
