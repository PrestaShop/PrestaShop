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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use CustomerMessage;
use CustomerThread;
use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServicesSummary;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerServicesSummary;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tools;

class CustomerServiceFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
    }

    /**
     * @When I add new customer thread :threadReference with following properties:
     *
     * @param string $threadReference
     * @param TableNode $table
     */
    public function createCustomerThread(string $threadReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $contactReference = $this->referenceToId($data['contactReference']);

        // Add this message in the customer thread
        $customerThread = new CustomerThread();
        $customerThread->id_contact = $contactReference;
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
        $customerThread = $this->getSharedStorage()->get($threadReference);

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
        $customerThread = $this->getSharedStorage()->get($threadReference);

        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing((int) $customerThread->id)
        );
        $messages = $customerThreadView->getMessages();

        $lastMessage = end($messages);

        if ($lastMessage->getMessage() !== $message) {
            throw new RuntimeException(sprintf('thread "%s" has "%s" latest message, but "%s" was expected.', $threadReference, $lastMessage->getMessage(), $message));
        }
    }

    /**
     * @When I update thread :threadReference status to :status
     *
     * @param string $threadReference
     */
    public function updateThreadStatus(string $threadReference, string $status): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = $this->getSharedStorage()->get($threadReference);

        $this->getCommandBus()->handle(
            new UpdateCustomerThreadStatusCommand(
                (int) $customerThread->id,
                $status
            )
        );
    }

    /**
     * @Then customer thread :threadReference should be :status
     *
     * @param string $threadReference
     */
    public function assertThreadStatus(string $threadReference, string $status): void
    {
        /** @var CustomerThread $customerThread */
        $customerThread = $this->getSharedStorage()->get($threadReference);

        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing((int) $customerThread->id)
        );

        $allPossibleActions = [
            CustomerThreadStatus::OPEN,
            CustomerThreadStatus::CLOSED,
            CustomerThreadStatus::PENDING_1,
            CustomerThreadStatus::PENDING_2,
        ];
        $expectedPossibleActions = array_diff($allPossibleActions, [$status]);

        $actions = array_map(function ($action) {
            return $action['value'];
        }, $customerThreadView->getActions());

        foreach ($actions as $action) {
            if (!in_array($action, $expectedPossibleActions)) {
                throw new RuntimeException(sprintf('thread "%s" should have action "%s" possible.', $threadReference, $action));
            }
        }

        if (in_array($status, $actions)) {
            throw new RuntimeException(sprintf('thread "%s" should not have action "%s" possible.', $threadReference, $status));
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
        $customerThread = $this->getSharedStorage()->get($threadReference);

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
        $customerThread = $this->getSharedStorage()->get($threadReference);

        try {
            $query = new GetCustomerThreadForViewing((int) $customerThread->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Thread %s exists, but it was expected to be deleted', $threadReference));
        } catch (CustomerThreadNotFoundException $e) {
            $this->getSharedStorage()->clear($threadReference);
        }
    }

    /**
     * @Then contact :contactReference should have :expectedThreads threads
     *
     * @param string $contactReference
     * @param int $expectedThreads
     */
    public function assertContactHasThreads(string $contactReference, int $expectedThreads): void
    {
        $contactId = $this->referenceToId($contactReference);

        /** @var CustomerServicesSummary $customerServiceSummaries */
        $customerServiceSummaries = $this->getQueryBus()->handle(
            new GetCustomerServicesSummary()
        );

        foreach ($customerServiceSummaries->getContacts() as $contact) {
            if ($contact->getContactId() !== $contactId) {
                continue;
            }

            if ($contact->getTotalThreads() !== $expectedThreads) {
                throw new NoExceptionAlthoughExpectedException(sprintf('Contact expected to have %s threads, but it had %s', $expectedThreads, $contact->getTotalThreads()));
            }
        }

        if (count($customerServiceSummaries->getStatistics()) !== 6) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Statistics expect to have 6 different text rows but it has %s', count($customerServiceSummaries['statistics'])));
        }
    }

    /**
     * @When /^I create a contact "(.+)" with following properties:$/
     */
    public function createContactUsingCommand($contactReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $data = $this->formatContactDataIfNeeded($data);
        $commandBus = $this->getCommandBus();

        $mandatoryFields = [
            'localisedTitles',
            'isMessageSavingEnabled',
        ];

        foreach ($mandatoryFields as $mandatoryField) {
            if (!array_key_exists($mandatoryField, $data)) {
                throw new Exception(sprintf('Mandatory property %s for contact has not been provided', $mandatoryField));
            }
        }

        $command = new AddContactCommand(
            $data['localisedTitles'],
            $data['isMessageSavingEnabled']
        );

        /** @var ContactId $id */
        $id = $commandBus->handle($command);

        $this->getSharedStorage()->set($contactReference, $id->getValue());
    }

    protected function formatContactDataIfNeeded(array $data): array
    {
        if (array_key_exists('localisedTitles', $data)) {
            $data['localisedTitles'] = [$this->getDefaultShopId() => $data['localisedTitles']];
        }
        if (array_key_exists('isMessageSavingEnabled', $data)) {
            $data['isMessageSavingEnabled'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['isMessageSavingEnabled']);
        }

        return $data;
    }

    /**
     * @When I update contact options with following properties:
     *
     * @param TableNode $table
     */
    public function updateContactOptions(TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        $contactOptionsHandler = CommonFeatureContext::getContainer()->get('prestashop.adapter.customer_service.contact_options.form_handler');

        $defaultLangMessage = $data['defaultMessage'];

        $contactOptionsHandler->save(
            [
                'PS_CUSTOMER_SERVICE_SIGNATURE' => $defaultLangMessage,
                'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => PrimitiveUtils::castStringBooleanIntoBoolean($data['allowFileUploading']),
            ]
        );
    }

    /**
     * @Then contact options should have the following properties:
     *
     * @param TableNode $table
     */
    public function assertIsCorrectContactOptions(TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        $defaultMessage = $this->configuration->get('PS_CUSTOMER_SERVICE_SIGNATURE');
        $isFileUploadingAllowed = (bool) $this->configuration->get('PS_CUSTOMER_SERVICE_FILE_UPLOAD');

        $expectedMessage = $data['defaultMessage'];
        $expectedIsFileUploadingAllowed = PrimitiveUtils::castStringBooleanIntoBoolean($data['allowFileUploading']);

        foreach ($expectedMessage as $langId => $message) {
            if ($defaultMessage[$langId] !== $message) {
                throw new NoExceptionAlthoughExpectedException(
                    sprintf('Default contact message is expected to be %s , but it is %s', $message, $defaultMessage[$langId])
                );
            }
        }

        if ($isFileUploadingAllowed !== $expectedIsFileUploadingAllowed) {
            throw new NoExceptionAlthoughExpectedException(
                sprintf('Allow file uploading is expected to be set to %s , but it is %s', $isFileUploadingAllowed, $expectedIsFileUploadingAllowed)
            );
        }
    }
}
