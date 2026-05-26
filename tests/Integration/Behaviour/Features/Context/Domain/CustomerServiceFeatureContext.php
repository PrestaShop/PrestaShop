<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use CustomerThread;
use Db;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Entity\CustomerMessage;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceListingStatistics;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tools;

class CustomerServiceFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new customer thread :threadReference with following properties:
     *
     * @param string $threadReference
     * @param TableNode $table
     */
    public function createCustomerThread(string $threadReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        // Add this message in the customer thread
        $customerThread = new CustomerThread();
        $customerThread->id_contact = 2;
        $customerThread->id_customer = 1;
        $customerThread->id_shop = $this->getDefaultShopId();
        $customerThread->id_order = 0;
        $customerThread->id_lang = 1;
        $customerThread->email = 'test@gmail.com';
        $customerThread->status = CustomerThreadStatus::OPEN;
        $customerThread->token = Tools::passwdGen(12);
        $customerThread->add();

        $this->getSharedStorage()->set($threadReference, (int) $customerThread->id);

        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerThread->id;
        $customerMessage->id_employee = 0;
        $customerMessage->message = $data['message'];
        $customerMessage->file_name = '';
        $customerMessage->ip_address = '';
        $customerMessage->private = false;
        $customerMessage->read = false;
        $customerMessage->add();
    }

    /**
     * @Given there are no customer threads
     */
    public function clearCustomerThreads(): void
    {
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'customer_message');
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'customer_thread');
    }

    /**
     * @When I add new customer thread :threadReference with status :status and message :message
     */
    public function createCustomerThreadWithStatus(string $threadReference, string $status, string $message): void
    {
        $customerThread = new CustomerThread();
        $customerThread->id_contact = 2;
        $customerThread->id_customer = 1;
        $customerThread->id_shop = $this->getDefaultShopId();
        $customerThread->id_order = 0;
        $customerThread->id_lang = 1;
        $customerThread->email = 'test@gmail.com';
        $customerThread->status = $status;
        $customerThread->token = Tools::passwdGen(12);
        $customerThread->add();

        $this->getSharedStorage()->set($threadReference, $customerThread);

        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerThread->id;
        $customerMessage->id_employee = 0;
        $customerMessage->message = $message;
        $customerMessage->file_name = '';
        $customerMessage->ip_address = '';
        $customerMessage->private = false;
        $customerMessage->read = false;
        $customerMessage->add();
    }

    /**
     * @Then customer service listing statistics should be:
     */
    public function assertListingStatistics(TableNode $table): void
    {
        $expected = $table->getRowsHash();

        /** @var CustomerServiceListingStatistics $stats */
        $stats = $this->getQueryBus()->handle(new GetCustomerServiceListingStatistics());

        $actual = [
            'total_threads' => $stats->getTotalThreads(),
            'open_threads' => $stats->getOpenThreads(),
            'pending_threads' => $stats->getPendingThreads(),
            'closed_threads' => $stats->getClosedThreads(),
            'customer_messages' => $stats->getCustomerMessages(),
            'employee_messages' => $stats->getEmployeeMessages(),
        ];

        foreach ($expected as $key => $value) {
            if (!array_key_exists($key, $actual)) {
                throw new RuntimeException(sprintf('Unknown statistic "%s" in expectations.', $key));
            }
            Assert::assertSame(
                (int) $value,
                $actual[$key],
                sprintf('Statistic "%s" should be %s, got %s.', $key, $value, $actual[$key])
            );
        }
    }

    /**
     * @When I respond to customer thread :threadReference with following properties:
     *
     * @param string $threadReference
     * @param TableNode $table
     */
    public function respondToCustomerThread(string $threadReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        // it executes to fast and the update date is the same as the original message so we can't find which message is the new one
        sleep(1);
        $this->getCommandBus()->handle(
            new ReplyToCustomerThreadCommand($this->referenceToId($threadReference), $data['reply_message'])
        );
    }

    /**
     * @Then customer thread :threadReference should have the latest message :message
     *
     * @param string $threadReference
     * @param string $message
     */
    public function assertThreadLatestMessage(string $threadReference, string $message): void
    {
        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing($this->referenceToId($threadReference))
        );
        $messages = $customerThreadView->getMessages();

        $lastMessage = end($messages);
        foreach ($messages as $newMessage) {
            $lastMessage = $newMessage->getDate() > $lastMessage->getDate() ? $newMessage : $lastMessage;
        }

        $lastMessage = end($messages);

        if ($lastMessage->getMessage() !== $message) {
            throw new RuntimeException(sprintf('thread "%s" has "%s" latest message, but "%s" was expected.', $threadReference, $lastMessage->getMessage(), $message));
        }
    }

    /**
     * @When /^I update thread "([^"]+)" status to (open|closed|pending1|pending2)$/
     */
    public function updateThreadStatus(string $threadReference, string $status): void
    {
        $this->dispatchUpdateThreadStatus($this->referenceToId($threadReference), $status);
    }

    /**
     * @When /^I update non-existent customer thread with id (\d+) status to (open|closed|pending1|pending2)$/
     */
    public function updateNonExistentThreadStatus(int $threadId, string $status): void
    {
        $this->dispatchUpdateThreadStatus($threadId, $status);
    }

    private function dispatchUpdateThreadStatus(int $threadId, string $status): void
    {
        try {
            $this->getCommandBus()->handle(new UpdateCustomerThreadStatusCommand($threadId, $status));
        } catch (CustomerServiceException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^customer thread "(.+)" should be (open|closed|pending1|pending2)$/
     */
    public function assertThreadStatus(string $threadReference, string $expectedStatus): void
    {
        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing($this->referenceToId($threadReference))
        );

        Assert::assertSame(
            $expectedStatus,
            $customerThreadView->getStatus(),
            sprintf(
                'Customer thread "%s" should have status "%s", got "%s".',
                $threadReference,
                $expectedStatus,
                $customerThreadView->getStatus()
            )
        );
    }

    /**
     * @When I delete thread :threadReference
     *
     * @param string $threadReference
     */
    public function deleteThread(string $threadReference): void
    {
        $this->getCommandBus()->handle(new DeleteCustomerThreadCommand($this->referenceToId($threadReference)));
    }

    /**
     * @When I delete non-existent customer thread with id :threadId
     */
    public function deleteNonExistentThread(int $threadId): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteCustomerThreadCommand($threadId));
        } catch (CustomerThreadNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I bulk delete customer threads: "([^"]*)"$/
     */
    public function bulkDeleteThreads(string $threadReferences): void
    {
        $references = PrimitiveUtils::castStringArrayIntoArray($threadReferences);

        $threadIds = array_map(
            fn (string $reference): int => $this->referenceToId($reference),
            $references
        );

        $this->getCommandBus()->handle(new BulkDeleteCustomerThreadCommand($threadIds));
    }

    /**
     * @When /^I bulk delete non-existent customer threads with ids ([0-9, ]+)$/
     */
    public function bulkDeleteNonExistentThreads(string $rawIds): void
    {
        $ids = array_map(
            'intval',
            array_filter(array_map('trim', explode(',', $rawIds)), static fn (string $value): bool => $value !== '')
        );

        try {
            $this->getCommandBus()->handle(new BulkDeleteCustomerThreadCommand($ids));
        } catch (CustomerThreadNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then thread :threadReference should be deleted
     *
     * @param string $threadReference
     */
    public function assertThreadIsDeleted(string $threadReference): void
    {
        try {
            $this->getQueryBus()->handle(
                new GetCustomerThreadForViewing($this->referenceToId($threadReference))
            );

            throw new NoExceptionAlthoughExpectedException(sprintf('Thread %s exists, but it was expected to be deleted', $threadReference));
        } catch (CustomerThreadNotFoundException $e) {
            $this->getSharedStorage()->clear($threadReference);
        }
    }

    /**
     * @Then I should get error that customer thread does not exist
     */
    public function assertLastErrorIsCustomerThreadNotFound(): void
    {
        $this->assertLastErrorIs(CustomerThreadNotFoundException::class);
    }

    /**
     * @Then I should get error that customer thread status update failed
     */
    public function assertLastErrorIsThreadStatusUpdateFailed(): void
    {
        $this->assertLastErrorIs(
            CustomerServiceException::class,
            CustomerServiceException::FAILED_TO_UPDATE_STATUS
        );
    }
}
