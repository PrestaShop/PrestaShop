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
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class RequestSqlFormHandler is responsible for creating RequestSql form
 */
class RequestSqlFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @param FormFactoryInterface $formFactory
     * @param CommandBus $commandBus
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        CommandBus $commandBus
    ) {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $data = [])
    {
        $builder = $this->formFactory->createBuilder()
            ->add('request_sql', RequestSqlType::class)
            ->setData($data)
        ;

        return $builder->getForm();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function save(array $data)
    {
        $errors = [];

        try {
            $addRequestSqlCommand = new AddSqlRequestCommand(
                $data['request_sql']['name'],
                $data['request_sql']['sql']
            );

            $this->commandBus->handle($addRequestSqlCommand);
        } catch (SqlRequestConstraintException $e) {
            $errors[] = $this->getHumanReadableConstraintErrorMessage($e);
        } catch (CannotAddSqlRequestException $e) {
            $errors[] = $this->getHumanReadableCreationErrorMessage();
        } catch (SqlRequestException $e) {
            $errors[] = $this->getHumanReadableCreationErrorMessage();
        }

        return $errors;
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
            return $this->getHumanReadableCreationErrorMessage();
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
    private function getHumanReadableCreationErrorMessage()
    {
        return [
            'key' => 'An error occurred while creating an object.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
