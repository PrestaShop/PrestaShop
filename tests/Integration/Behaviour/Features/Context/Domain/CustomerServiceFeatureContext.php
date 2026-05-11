<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use CustomerThread;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Entity\CustomerMessage;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
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

        $this->getSharedStorage()->set($threadReference, $customerThread);

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
     * @When I respond to customer thread :threadReference with following properties:
     *
     * @param string $threadReference
     * @param TableNode $table
     */
    public function respondToCustomerThread(string $threadReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        // it executes to fast and the update date is the same as the original message so we can't find which message is the new one
        sleep(1);
        $this->getCommandBus()->handle(
            new ReplyToCustomerThreadCommand((int) $customerThread->id, $data['reply_message'])
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
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing((int) $customerThread->id)
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
     * Resolves a thread reference into a numeric id. References that exist in
     * `SharedStorage` resolve to the stored thread's id; otherwise the
     * reference is treated as a raw id (used by error scenarios that operate
     * on a thread that does not exist).
     */
    private function resolveThreadId(string $threadReference): int
    {
        if (SharedStorage::getStorage()->exists($threadReference)) {
            /** @var CustomerThread $customerThread */
            $customerThread = SharedStorage::getStorage()->get($threadReference);

            return (int) $customerThread->id;
        }

        return (int) $threadReference;
    }

    /**
     * @When /^I update thread "([^"]+)" status to (open|closed|pending1|pending2)$/
     */
    public function updateThreadStatus(string $threadReference, string $status): void
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateCustomerThreadStatusCommand($this->resolveThreadId($threadReference), $status)
            );
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
            new GetCustomerThreadForViewing($this->resolveThreadId($threadReference))
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
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        $this->getCommandBus()->handle(new DeleteCustomerThreadCommand((int) $customerThread->id));
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
            static fn (string $reference): int => (int) SharedStorage::getStorage()->get($reference)->id,
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
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        try {
            $query = new GetCustomerThreadForViewing((int) $customerThread->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Thread %s exists, but it was expected to be deleted', $threadReference));
        } catch (CustomerThreadNotFoundException $e) {
            SharedStorage::getStorage()->clear($threadReference);
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
