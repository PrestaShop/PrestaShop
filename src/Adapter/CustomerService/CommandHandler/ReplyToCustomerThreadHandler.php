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

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Contact;
use Context;
use Customer;
use CustomerMessage;
use CustomerThread;
use Language;
use Mail;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\ReplyToCustomerThreadHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use ShopUrl;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * @internal
 */
final class ReplyToCustomerThreadHandler implements ReplyToCustomerThreadHandlerInterface
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

        $this->createCustomerMessage(
            $customerThread,
            $command->getReplyMessage()
        );

        $replyWasSent = $this->sendReplyEmail($customerThread, $command);

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
        $customerMessage->ip_address = (int) ip2long(Tools::getRemoteAddr());
        $customerMessage->message = $replyMessage;

        if (false === $customerMessage->validateField('message', $customerMessage->message)) {
            throw new CustomerServiceException('Invalid reply message');
        }

        if (false !== $customerMessage->add()) {
            throw new CustomerServiceException('Failed to add customer message');
        }

        return $customerMessage;
    }

    /**
     * @param CustomerThread $customerThread
     * @param ReplyToCustomerThreadCommand $command
     *
     * @return bool
     */
    private function sendReplyEmail(CustomerThread $customerThread, ReplyToCustomerThreadCommand $command)
    {
        $customer = new Customer($customerThread->id_customer);

        $params = [
            '{reply}' => Tools::nl2br($command->getReplyMessage()),
            '{link}' => Tools::url(
                $this->context->link->getPageLink('contact', true, null, null, false, $customerThread->id_shop),
                'id_customer_thread=' . (int) $customerThread->id . '&token=' . $customerThread->token
            ),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
        ];

        $contact = new Contact((int) $customerThread->id_contact, (int) $customerThread->id_lang);

        if (Validate::isLoadedObject($contact)) {
            $fromName = $contact->name;
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
