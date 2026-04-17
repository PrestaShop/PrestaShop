<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * @internal
 */
#[AsCommandHandler]
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

        if (0 === $statement->executeStatement()) {
            throw new CustomerServiceException('Failed to update customer thread status.', CustomerServiceException::FAILED_TO_UPDATE_STATUS);
        }
    }
}
