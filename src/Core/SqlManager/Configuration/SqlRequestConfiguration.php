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

namespace PrestaShop\PrestaShop\Core\SqlManager\Configuration;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\SaveSqlRequestSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestSettingsConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;

/**
 * Class RequestSqlConfiguration is responsible for RequestSql configuration.
 */
final class SqlRequestConfiguration implements DataConfigurationInterface
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
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        /** @var SqlRequestSettings $sqlRequestSettings */
        $sqlRequestSettings = $this->queryBus->handle(new GetSqlRequestSettings());

        return [
            'default_file_encoding' => $sqlRequestSettings->getFileEncoding(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            try {
                $command = new SaveSqlRequestSettingsCommand(
                    $configuration['default_file_encoding']
                );

                $this->commandBus->handle($command);
            } catch (SqlRequestSettingsConstraintException $e) {
                $errors = $this->handleUpdateException($e);
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset($configuration['default_file_encoding']);
    }

    /**
     * Handle exception when configuration update fails.
     *
     * @param SqlRequestSettingsConstraintException $e
     *
     * @return array Array of errors
     */
    private function handleUpdateException(SqlRequestSettingsConstraintException $e)
    {
        $code = $e->getCode();

        $errorMessages = [
            SqlRequestSettingsConstraintException::INVALID_FILE_ENCODING => [
                'key' => 'The %s field is invalid.',
                'parameters' => ['default_file_encoding'],
                'domain' => 'Admin.Notifications.Error',
            ],
            SqlRequestSettingsConstraintException::NOT_SUPPORTED_FILE_ENCODING => [
                'key' => 'The %s field is invalid.',
                'parameters' => ['default_file_encoding'],
                'domain' => 'Admin.Notifications.Error',
            ],
        ];

        return $errorMessages[$code];
    }
}
