<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use Contact;
use Context;
use Customer;
use CustomerMessage;
use CustomerThread;
use Language;
use Mail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use ShopUrl;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
class ReplyToCustomerThreadHandler implements ReplyToCustomerThreadHandlerInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->translator = $context->getTranslator();
    }

    /**
     * @param ReplyToCustomerThreadCommand $command
     */
    public function handle(ReplyToCustomerThreadCommand $command)
    {
        $customerThread = new CustomerThread(
            $command->getCustomerThreadId()->getValue()
        );

        ShopUrl::cacheMainDomainForShop((int) $customerThread->id_shop);

        $customerMessage = $this->createCustomerMessage(
            $customerThread,
            $command->getReplyMessage()
        );

        $replyWasSent = $this->sendReplyEmail($customerThread, $customerMessage);

        if ($replyWasSent) {
            $customerThread->status = CustomerThreadStatus::CLOSED;
            $customerThread->update();
        }
    }

    /**
     * @param CustomerThread $customerThread
     * @param string $replyMessage
     *
     * @return CustomerMessage
     */
    private function createCustomerMessage(CustomerThread $customerThread, $replyMessage)
    {
        $customerMessage = new CustomerMessage();
        $customerMessage->id_employee = (int) $this->context->employee->id;
        $customerMessage->id_customer_thread = $customerThread->id;
        $customerMessage->ip_address = (string) (int) ip2long(Tools::getRemoteAddr());
        $customerMessage->message = $replyMessage;

        if (false === $customerMessage->validateField('message', $customerMessage->message)) {
            throw new CustomerServiceException('Invalid reply message', CustomerServiceException::FAILED_TO_ADD_CUSTOMER_MESSAGE);
        }

        if (false === $customerMessage->add()) {
            throw new CustomerServiceException('Failed to add customer message.', CustomerServiceException::FAILED_TO_ADD_CUSTOMER_MESSAGE);
        }

        return $customerMessage;
    }

    /**
     * @param CustomerThread $customerThread
     * @param CustomerMessage $customerMessage
     *
     * @return bool
     */
    private function sendReplyEmail(CustomerThread $customerThread, CustomerMessage $customerMessage)
    {
        $customer = new Customer($customerThread->id_customer);

        $params = [
            '{reply}' => Tools::nl2br($customerMessage->message),
            '{link}' => Tools::url(
                $this->context->link->getPageLink('contact', null, null, null, false, $customerThread->id_shop),
                'id_customer_thread=' . (int) $customerThread->id . '&token=' . $customerThread->token
            ),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
        ];

        $contact = new Contact((int) $customerThread->id_contact, (int) $customerThread->id_lang);

        if (Validate::isLoadedObject($contact)) {
            $fromName = is_array($contact->name) ? $contact->name[array_key_first($contact->name)] : $contact->name;
            $fromEmail = $contact->email;
        } else {
            $fromName = null;
            $fromEmail = null;
        }

        $language = new Language((int) $customerThread->id_lang);

        return Mail::Send(
            (int) $customerThread->id_lang,
            'reply_msg',
            $this->translator->trans(
                'An answer to your message is available #ct%thread_id% #tc%thread_token%',
                [
                    '%thread_id%' => $customerThread->id,
                    '%thread_token%' => $customerThread->token,
                ],
                'Emails.Subject',
                $language->locale
            ),
            $params,
            $customerThread->email,
            null,
            $fromEmail,
            $fromName,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            $customerThread->id_shop
        );
    }
}
