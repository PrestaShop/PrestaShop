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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use League\Tactician\CommandBus;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotEditSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditingQuery;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class RequestSqlFormDataProvider is responsible for getting/saving RequestSql form data
 */
class RequestSqlFormDataProvider
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var CommandBus
     */
    private $queryBus;

    /**
     * @param CommandBus $commandBus
     * @param CommandBus $queryBus
     */
    public function __construct(
        CommandBus $commandBus,
        CommandBus $queryBus
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * Get RequestSql form data
     *
     * @param int $requestSqlId
     *
     * @return array
     */
    public function getData($requestSqlId)
    {
        try {
            $getRequestSqlForEditingQuery = new GetSqlRequestForEditingQuery($requestSqlId);

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
     * Save form data for RequestSql
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
        }  catch (SqlRequestConstraintException $e) {
            $errors[] = $this->getHumanReadableConstraintErrorMessage($e);
        } catch (SqlRequestNotFoundException $e) {
            $errors[] = $this->getHumanReadableNotFoundException();
        } catch (CannotAddSqlRequestException $e) {
            $errors[] = $this->getHumanReadableCreateErrorMessage();
        } catch (CannotEditSqlRequestException $e) {
            $errors[] = $this->getHumanReadableUpdateErrorMessage();
        } catch (SqlRequestException $e) {
            $errors[] = $this->getHumanReadableGenericErrorMessage();
        }

        return $errors;
    }

    /**
     * @param array $requestSqlData
     *
     * @return AddSqlRequestCommand
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
     */
    private function getEditRequestSqlCommand(array $requestSqlData)
    {
        return new EditSqlRequestCommand(
            new SqlRequestId($requestSqlData['id']),
            $requestSqlData['name'],
            $requestSqlData['sql']
        );
    }

    /**
     * @param SqlRequestConstraintException $e
     *
     * @return array Constraint error message prepared for translation
     */
    private function getHumanReadableConstraintErrorMessage(SqlRequestConstraintException $e)
    {
        switch ($e->getCode()) {
            case SqlRequestConstraintException::INVALID_NAME_ERROR:
                $invalidField = 'name';
                break;
            case SqlRequestConstraintException::INVALID_SQL_QUERY_ERROR:
            case SqlRequestConstraintException::MALFORMED_SQL_QUERY_ERROR:
                $invalidField = 'sql';
                break;
            default:
                $invalidField = null;
                break;
        }

        if (null === $invalidField) {
            return [
                'key' => 'Invalid data supplied.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];
        }

        return [
            'key' => 'The %s field is invalid.',
            'parameters' => [
                $invalidField
            ],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get human readable error message when failed to create RequestSql
     *
     * @return array
     */
    private function getHumanReadableCreateErrorMessage()
    {
        return [
            'key' => 'An error occurred while creating an object.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get human readable error message when failed to create RequestSql
     *
     * @return array
     */
    private function getHumanReadableUpdateErrorMessage()
    {
        return [
            'key' => 'An error occurred while creating an object.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get general error when something goes wrong
     *
     * @return array
     */
    private function getHumanReadableGenericErrorMessage()
    {
        return [
            'key' => 'Unexpected error occurred.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get error message when RequestSql is not found
     */
    private function getHumanReadableNotFoundException()
    {
        return [
            'key' => 'The object cannot be loaded (or found)',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
