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

namespace PrestaShop\PrestaShop\Core\SqlManager\Form;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotEditSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObjectFormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class SqlRequestFormDataHandler.
 */
final class SqlRequestFormDataHandler implements IdentifiableObjectFormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param CommandBusInterface $commandBus
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        CommandBusInterface $commandBus,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->commandBus = $commandBus;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $sqlRequestFormData, $sqlRequestId = null)
    {
        $errors = [];

        try {
            $command = $sqlRequestId ?
                $this->getEditRequestSqlCommand($sqlRequestId, $sqlRequestFormData['request_sql']) :
                $this->getAddRequestSqlCommand($sqlRequestFormData['request_sql']);

            $this->commandBus->handle($command);

            $this->hookDispatcher->dispatchWithParameters('actionSqlRequestSave', [
                'id' => $sqlRequestId,
                'data' => $sqlRequestFormData,
            ]);
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
     * @param int $sqlRequestId
     * @param array $requestSqlData
     *
     * @return EditSqlRequestCommand
     *
     * @throws SqlRequestConstraintException
     */
    private function getEditRequestSqlCommand($sqlRequestId, array $requestSqlData)
    {
        return (new EditSqlRequestCommand(new SqlRequestId($sqlRequestId)))
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
