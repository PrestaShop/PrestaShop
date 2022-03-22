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

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Contact;
use Context;
use CustomerMessage;
use Db;
use Employee;
use Mail;
use Order;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\ForwardCustomerThreadHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * @internal
 */
final class ForwardCustomerThreadHandler implements ForwardCustomerThreadHandlerInterface
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
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param Context $context
     * @param ConfigurationInterface $configuration
     */
    public function __construct(Context $context, ConfigurationInterface $configuration)
    {
        $this->context = $context;
        $this->translator = $this->context->getTranslator();
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ForwardCustomerThreadCommand $command)
    {
        if ($command->forwardToEmployee()) {
            $this->forwardToEmployee($command);
        } else {
            $this->forwardToSomeoneElse($command);
        }
    }

    /**
     * Forward customer thread to another employee
     *
     * @param ForwardCustomerThreadCommand $command
     */
    private function forwardToEmployee(ForwardCustomerThreadCommand $command)
    {
        $customerThreadMessage = $this->getCustomerThreadMessage($command->getCustomerThreadId());

        $content = $this->renderMessage(
            $customerThreadMessage,
            $command->getEmployeeId()->getValue()
        );

        $customerMessage = $this->createCustomerMessage($command);
        $employee = new Employee($command->getEmployeeId()->getValue());

        $params = [
            '{messages}' => stripslashes($content),
            '{employee}' => $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
            '{comment}' => stripslashes(Tools::nl2br($command->getComment())),
            '{firstname}' => $employee->firstname,
            '{lastname}' => $employee->lastname,
        ];

        $forwardEmailSent = Mail::Send(
            $this->context->language->id,
            'forward_msg',
            $this->context->getTranslator()->trans(
                'Fwd: Customer message',
                [],
                'Emails.Subject',
                $this->context->language->locale
            ),
            $params,
            $employee->email,
            $employee->firstname . ' ' . $employee->lastname,
            $this->context->employee->email,
            $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
            null,
            null,
            _PS_MAIL_DIR_,
            true
        );

        if ($forwardEmailSent) {
            $customerMessage->private = true;
            $customerMessage->message = sprintf(
                '%s %s %s %s %s %s',
                $this->translator->trans('Message forwarded to', [], 'Admin.Catalog.Feature'),
                $employee->firstname,
                $employee->lastname,
                PHP_EOL,
                $this->translator->trans('Comment:', [], 'Admin.Catalog.Feature'),
                $command->getComment()
            );

            $customerMessage->add();
        }
    }

    /**
     * Forward customer thread to someone else
     *
     * @param ForwardCustomerThreadCommand $command
     */
    private function forwardToSomeoneElse(ForwardCustomerThreadCommand $command)
    {
        $customerThreadMessage = $this->getCustomerThreadMessage($command->getCustomerThreadId());

        $content = $this->renderMessage($customerThreadMessage);

        $params = [
            '{messages}' => Tools::nl2br(stripslashes($content)),
            '{employee}' => $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
            '{comment}' => stripslashes($command->getComment()),
            '{firstname}' => '',
            '{lastname}' => '',
        ];

        $customerMessage = $this->createCustomerMessage($command);

        $forwardEmailSent = Mail::Send(
            $this->context->language->id,
            'forward_msg',
            $this->translator->trans(
                'Fwd: Customer message',
                [],
                'Emails.Subject',
                $this->context->language->locale
            ),
            $params,
            $command->getEmail()->getValue(),
            null,
            $this->context->employee->email,
            $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
            null,
            null,
            _PS_MAIL_DIR_,
            true
        );

        if ($forwardEmailSent) {
            $customerMessage->message = sprintf(
                '%s %s %s %s %s',
                $this->translator->trans('Message forwarded to', [], 'Admin.Catalog.Feature'),
                $command->getEmail()->getValue(),
                PHP_EOL,
                $this->translator->trans('Comment:', [], 'Admin.Catalog.Feature'),
                $command->getComment()
            );

            $customerMessage->add();
        }
    }

    /**
     * @param array $message
     * @param int|null $id_employee
     *
     * @return string
     */
    protected function renderMessage(array $message, $id_employee = null)
    {
        $tpl = $this->context->smarty->createTemplate(
            'controllers' . DIRECTORY_SEPARATOR . 'customer_threads/message.tpl',
            $this->context->smarty
        );

        $contacts = Contact::getContacts($this->context->language->id);
        $contact_array = [];

        foreach ($contacts as $contact) {
            $contact_array[$contact['id_contact']] = [
                'id_contact' => $contact['id_contact'],
                'name' => $contact['name'],
            ];
        }

        $contacts = $contact_array;

        if (!empty($message['id_product'])
            && empty($message['employee_name'])
        ) {
            $id_order_product = Order::getIdOrderProduct(
                (int) $message['id_customer'],
                (int) $message['id_product']
            );
        }

        $message['date_add'] = Tools::displayDate($message['date_add'], true);
        $message['user_agent'] = strip_tags($message['user_agent']);
        $message['message'] = $this->replaceUrlsWithTags($message['message']);

        $isValidOrderId = true;
        $order = new Order((int) $message['id_order']);

        if (!Validate::isLoadedObject($order)) {
            $isValidOrderId = false;
        }

        $baseAdminLink = Tools::getAdminUrl(basename(_PS_ADMIN_DIR_));

        $threadUrl = $baseAdminLink . '/' . $this->context->link->getAdminLink('AdminCustomerThreads', true, [], [
            'id_customer_thread' => (int) $message['id_customer_thread'],
            'viewcustomer_thread' => 1,
        ]);

        $tpl->assign([
            'thread_url' => $threadUrl,
            'link' => $this->context->link,
            'token' => Tools::getAdminToken(
                'AdminCustomerThreads' . (int) $message['id_customer_thread'] . (int) $this->context->employee->id
            ),
            'message' => $message,
            'id_order_product' => isset($id_order_product) ? $id_order_product : null,
            'email' => true,
            'id_employee' => $id_employee,
            'PS_SHOP_NAME' => $this->configuration->get('PS_SHOP_NAME'),
            'file_name' => file_exists(_PS_UPLOAD_DIR_ . $message['file_name']),
            'contacts' => $contacts,
            'is_valid_order_id' => $isValidOrderId,
        ]);

        return $tpl->fetch();
    }

    /**
     * @param CustomerThreadId $customerThreadId
     *
     * @return array
     */
    private function getCustomerThreadMessage(CustomerThreadId $customerThreadId)
    {
        return Db::getInstance()->getRow('
            SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
                CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
            FROM ' . _DB_PREFIX_ . 'customer_thread ct
            LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
                ON (ct.id_customer_thread = cm.id_customer_thread)
            LEFT JOIN ' . _DB_PREFIX_ . 'contact_lang cl
                ON (cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT OUTER JOIN ' . _DB_PREFIX_ . 'employee e
                ON e.id_employee = cm.id_employee
            LEFT OUTER JOIN ' . _DB_PREFIX_ . 'customer c
                ON (c.email = ct.email)
            WHERE ct.id_customer_thread = ' . (int) $customerThreadId->getValue() . '
            ORDER BY cm.date_add DESC
		');
    }

    /**
     * @param ForwardCustomerThreadCommand $command
     *
     * @return CustomerMessage
     */
    private function createCustomerMessage(ForwardCustomerThreadCommand $command)
    {
        $customerMessage = new CustomerMessage();
        $customerMessage->id_employee = (int) $this->context->employee->id;
        $customerMessage->id_customer_thread = (int) $command->getCustomerThreadId()->getValue();
        $customerMessage->ip_address = (string) (int) ip2long(Tools::getRemoteAddr());

        if (false === $customerMessage->validateField('message', $command->getComment())) {
            throw new CustomerServiceException(sprintf('Comment "%s" is not valid.', $command->getComment()));
        }

        return $customerMessage;
    }

    /**
     * Replaces URLs with <a> tags in string.
     *
     * @param string $text
     *
     * @return string
     */
    private function replaceUrlsWithTags($text)
    {
        return preg_replace(
            '/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui',
            '<a href="\1">\1</a>\2',
            html_entity_decode(
                $text,
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }
}
