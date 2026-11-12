<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Context;
use CustomerMessage;
use Employee;
use Mail;
use PrestaShop\PrestaShop\Adapter\CustomerService\ForwardedMessageRenderer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\ForwardCustomerThreadHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * @internal
 */
#[AsCommandHandler]
final class ForwardCustomerThreadHandler implements ForwardCustomerThreadHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(
        private readonly Context $context,
        private readonly ForwardedMessageRenderer $forwardedMessageRenderer,
    ) {
        $this->translator = $this->context->getTranslator();
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
        $content = $this->forwardedMessageRenderer->render($command->getCustomerThreadId(), true);

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
        $content = $this->forwardedMessageRenderer->render($command->getCustomerThreadId(), false);

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
            throw new CustomerServiceException(
                sprintf('Comment "%s" is not valid.', $command->getComment()),
                CustomerServiceException::INVALID_COMMENT
            );
        }

        return $customerMessage;
    }
}
