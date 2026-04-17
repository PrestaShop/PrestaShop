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
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
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
