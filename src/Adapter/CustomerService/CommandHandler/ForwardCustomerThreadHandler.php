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

use Context;
use CustomerMessage;
use Db;
use Employee;
use Mail;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\ForwardCustomerThreadHandlerInterface;
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
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->translator = $this->context->getTranslator();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ForwardCustomerThreadCommand $command)
    {
        $customerThreadMessage = $this->getCustomerThreadMessage($command->getCustomerThreadId());

        $content = $this->renderMessage(
            $customerThreadMessage,
            true,
            $command->getEmployeeId()->getValue()
        );

        $current_employee = $this->context->employee;
        $id_employee = (int) Tools::getValue('id_employee_forward');
        $employee = new Employee($id_employee);
        $email = Tools::getValue('email');
        $message = Tools::getValue('message_forward');

        if ($command->forwardToEmployee()) {
            $this->forwardToEmployee($command, $content);
        } elseif ($email && Validate::isEmail($email)) {
            $this->forwardToSomeoneElse();
        }
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
     * @param $message
     * @param bool $email
     * @param null $id_employee
     *
     * @return string
     */
    protected function renderMessage($message, $email = false, $id_employee = null)
    {
        $tpl = $this->createTemplate('message.tpl');

        $contacts = Contact::getContacts($this->context->language->id);
        foreach ($contacts as $contact) {
            $contact_array[$contact['id_contact']] = array('id_contact' => $contact['id_contact'], 'name' => $contact['name']);
        }
        $contacts = $contact_array;

        if (!$email) {
            if (!empty($message['id_product']) && empty($message['employee_name'])) {
                $id_order_product = Order::getIdOrderProduct((int) $message['id_customer'], (int) $message['id_product']);
            }
        }
        $message['date_add'] = Tools::displayDate($message['date_add'], null, true);
        $message['user_agent'] = strip_tags($message['user_agent']);
        $message['message'] = preg_replace(
            '/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui',
            '<a href="\1">\1</a>\2',
            html_entity_decode(
                $message['message'],
                ENT_QUOTES,
                'UTF-8'
            )
        );

        $is_valid_order_id = true;
        $order = new Order((int) $message['id_order']);

        if (!Validate::isLoadedObject($order)) {
            $is_valid_order_id = false;
        }

        $tpl->assign(array(
            'thread_url' => Tools::getAdminUrl(basename(_PS_ADMIN_DIR_) . '/' .
                $this->context->link->getAdminLink('AdminCustomerThreads') . '&amp;id_customer_thread='
                . (int) $message['id_customer_thread'] . '&amp;viewcustomer_thread=1'),
            'link' => Context::getContext()->link,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'message' => $message,
            'id_order_product' => isset($id_order_product) ? $id_order_product : null,
            'email' => $email,
            'id_employee' => $id_employee,
            'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
            'file_name' => file_exists(_PS_UPLOAD_DIR_ . $message['file_name']),
            'contacts' => $contacts,
            'is_valid_order_id' => $is_valid_order_id,
        ));

        return $tpl->fetch();
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
        $customerMessage->ip_address = (int) ip2long(Tools::getRemoteAddr());

        if (false === $customerMessage->validateField('message', $command->getComment())) {
            // @todo: throw exception
        }

        return $customerMessage;
    }

    private function forwardToEmployee(ForwardCustomerThreadCommand $command, $renderedMessage)
    {
        $customerMessage = $this->createCustomerMessage($command);
        $employee = new Employee($command->getEmployeeId()->getValue());

        $params = [
            '{messages}' => stripslashes($renderedMessage),
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
            $customerMessage->private = 1;
            $customerMessage->message = sprintf(
                '%s %s %s \\n %s %s',
                $this->translator->trans('Message forwarded to', [], 'Admin.Catalog.Feature'),
                $employee->firstname,
                $employee->lastname,
                $this->translator->trans('Comment:'),
                $command->getComment()
            );

            $customerMessage->add();
        }
    }

    private function forwardToSomeoneElse()
    {
        $params = [
            '{messages}' => Tools::nl2br(stripslashes($output)),
            '{employee}' => $current_employee->firstname . ' ' . $current_employee->lastname,
            '{comment}' => stripslashes($_POST['message_forward']),
            '{firstname}' => '',
            '{lastname}' => '',
        ];

        if () {
            $customerMessage->message = $this->trans('Message forwarded to', array(), 'Admin.Catalog.Feature') . ' ' . $email . "\n" . $this->trans('Comment:') . ' ' . $message;
            $customerMessage->add();
        }
    }

    private function sendForwardEmail()
    {
        $params = [
            '{messages}' => Tools::nl2br(stripslashes($output)),
            '{employee}' => $current_employee->firstname . ' ' . $current_employee->lastname,
            '{comment}' => stripslashes($_POST['message_forward']),
            '{firstname}' => '',
            '{lastname}' => '',
        ];

        return Mail::Send(
            $this->context->language->id,
            'forward_msg',
            $this->translator->trans(
                'Fwd: Customer message',
                [],
                'Emails.Subject',
                $this->context->language->locale
            ),
            $params,
            $email,
            null,
            $current_employee->email,
            $current_employee->firstname . ' ' . $current_employee->lastname,
            null,
            null,
            _PS_MAIL_DIR_,
            true
        );
    }
}
