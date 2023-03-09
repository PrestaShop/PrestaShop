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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use Doctrine\DBAL\Driver\Connection;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * @internal
 */
class UpdateCustomerThreadStatusHandler implements UpdateCustomerThreadStatusHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCustomerThreadStatusCommand $command)
    {
        $statement = $this->connection->prepare('
            UPDATE ' . $this->dbPrefix . 'customer_thread
            SET status = :status
		    WHERE id_customer_thread = :id_customer_thread
		    LIMIT 1
        ');

        $statement->bindValue(':status', $command->getCustomerThreadStatus()->getValue());
        $statement->bindValue(':id_customer_thread', $command->getCustomerThreadId()->getValue());

        if (false === $statement->execute()) {
            throw new CustomerServiceException('Failed to update customer thread status.', CustomerServiceException::FAILED_TO_UPDATE_STATUS);
        }
    }
}
