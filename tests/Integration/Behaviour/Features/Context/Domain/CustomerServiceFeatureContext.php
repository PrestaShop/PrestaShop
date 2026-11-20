<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use CustomerThread;
use Employee;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\CustomerService\ForwardedMessageRenderer;
use PrestaShop\PrestaShop\Adapter\Entity\CustomerMessage;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tools;

class CustomerServiceFeatureContext extends AbstractDomainFeatureContext
{
    private const FORWARDED_EMAIL_KEY = 'customer_service_forwarded_email';

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
     * @When I update thread :threadReference status to open
     *
     * @param string $threadReference
     */
    public function updateThreadStatus(string $threadReference): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        $this->getCommandBus()->handle(
            new UpdateCustomerThreadStatusCommand(
                (int) $customerThread->id,
                CustomerThreadStatus::OPEN
            )
        );
    }

    /**
     * @Then /^customer thread "(.+)" should be (open|closed|pending1|pending2)$/
     *
     * @param string $threadReference
     */
    public function assertThreadStatus(string $threadReference, string $expectedStatus): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing((int) $customerThread->id)
        );

        $actions = $customerThreadView->getActions();
        foreach ([CustomerThreadStatus::OPEN, CustomerThreadStatus::PENDING_1, CustomerThreadStatus::PENDING_2, CustomerThreadStatus::CLOSED] as $possibleAction) {
            if ($expectedStatus === $possibleAction) {
                Assert::assertArrayNotHasKey($possibleAction, $actions, sprintf('thread "%s" should not have action "%s" possible.', $threadReference, CustomerThreadStatus::OPEN));
            } else {
                Assert::assertArrayHasKey($possibleAction, $actions, sprintf('thread "%s" should have action "%s" possible.', $threadReference, CustomerThreadStatus::OPEN));
            }
        }
    }

    /**
     * @When I forward customer thread :threadReference to :email with comment :comment
     */
    public function forwardThreadToEmail(string $threadReference, string $email, string $comment): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        $this->handleAndCatchForwardedEmail(
            ForwardCustomerThreadCommand::toSomeoneElse((int) $customerThread->id, $email, $comment)
        );
    }

    /**
     * @When I forward customer thread :threadReference to employee :employeeEmail with comment :comment
     */
    public function forwardThreadToEmployee(string $threadReference, string $employeeEmail, string $comment): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);
        $employee = (new Employee())->getByEmail($employeeEmail);

        $this->handleAndCatchForwardedEmail(
            ForwardCustomerThreadCommand::toAnotherEmployee((int) $customerThread->id, (int) $employee->id, $comment)
        );
    }

    /**
     * @Then the forwarded email should be sent to :address
     */
    public function assertForwardedEmailRecipient(string $address): void
    {
        Assert::assertSame($address, $this->getSharedStorage()->get(self::FORWARDED_EMAIL_KEY)['to'][0]['address']);
    }

    /**
     * @Then /^the forwarded email should (contain|not contain) "(.+)"$/
     */
    public function assertForwardedEmailContent(string $assertion, string $text): void
    {
        $html = $this->getSharedStorage()->get(self::FORWARDED_EMAIL_KEY)['html'];

        if ('contain' === $assertion) {
            Assert::assertStringContainsString($text, $html);
        } else {
            Assert::assertStringNotContainsString($text, $html);
        }
    }

    private function handleAndCatchForwardedEmail(ForwardCustomerThreadCommand $command): void
    {
        $mailDevClient = $this->getMailDevClient();
        $mailDevClient->deleteAllEmails();

        $this->getCommandBus()->handle($command);

        $emails = $mailDevClient->waitForEmails(1);
        Assert::assertCount(1, $emails);
        $this->getSharedStorage()->set(self::FORWARDED_EMAIL_KEY, $emails[0]);
    }

    /**
     * @Then /^the forwarded copy of customer thread "(.+)" for (an employee|someone else) should (contain|not contain) "(.+)"$/
     */
    public function assertForwardedCopyContent(string $threadReference, string $recipient, string $assertion, string $text): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);

        /** @var ForwardedMessageRenderer $renderer */
        $renderer = $this->getContainer()->get(ForwardedMessageRenderer::class);
        $content = $renderer->render(new CustomerThreadId((int) $customerThread->id), 'an employee' === $recipient);

        if ('contain' === $assertion) {
            Assert::assertStringContainsString($text, $content);
        } else {
            Assert::assertStringNotContainsString($text, $content);
        }
    }

    /**
     * @When I forward customer thread with id :customerThreadId to :email with comment :comment
     */
    public function forwardThreadByIdToEmail(int $customerThreadId, string $email, string $comment): void
    {
        try {
            $this->getCommandBus()->handle(
                ForwardCustomerThreadCommand::toSomeoneElse($customerThreadId, $email, $comment)
            );
        } catch (CustomerThreadNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then customer thread :threadReference latest message should contain :text
     */
    public function assertThreadLatestMessageContains(string $threadReference, string $text): void
    {
        Assert::assertStringContainsString($text, $this->getLatestMessage($threadReference)['message']);
    }

    /**
     * @Then /^customer thread "(.+)" latest message should be (private|public)$/
     */
    public function assertThreadLatestMessageVisibility(string $threadReference, string $visibility): void
    {
        Assert::assertSame('private' === $visibility, (bool) $this->getLatestMessage($threadReference)['private']);
    }

    /**
     * Messages added within the same second share their date, so the highest id is the latest one.
     *
     * @return array<string, mixed>
     */
    private function getLatestMessage(string $threadReference): array
    {
        /** @var CustomerThread $customerThread */
        $customerThread = SharedStorage::getStorage()->get($threadReference);
        $messages = CustomerThread::getMessageCustomerThreads((int) $customerThread->id);
        usort($messages, static fn (array $a, array $b): int => (int) $a['id_customer_message'] <=> (int) $b['id_customer_message']);

        return end($messages);
    }

    /**
     * @Then I should get an error that the customer thread was not found
     */
    public function assertCustomerThreadNotFoundError(): void
    {
        $this->assertLastErrorIs(CustomerThreadNotFoundException::class);
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
}
