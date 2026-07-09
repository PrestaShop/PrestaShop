<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use Contact;
use Context;
use Customer;
use CustomerMessage;
use CustomerThread;
use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerMessageSyncImapRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\SyncCustomerServiceImapMailboxCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\ImapSyncResult;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Ports the legacy `AdminCustomerThreadsController::syncImap()` mailbox
 * synchronization: fetches new messages from the configured IMAP mailbox,
 * matches them to existing customer threads via the `#ct<id> #tc<token>`
 * subject markers, and creates new threads for unrecognized senders when
 * `PS_SAV_IMAP_CREATE_THREADS` is enabled.
 *
 * @internal
 */
#[AsCommandHandler]
class SyncCustomerServiceImapMailboxHandler implements SyncCustomerServiceImapMailboxHandlerInterface
{
    private const OPTION_CONFIGURATION_KEYS = [
        'PS_SAV_IMAP_OPT_POP3' => '/pop3',
        'PS_SAV_IMAP_OPT_NORSH' => '/norsh',
        'PS_SAV_IMAP_OPT_SSL' => '/ssl',
        'PS_SAV_IMAP_OPT_VALIDATE-CERT' => '/validate-cert',
        'PS_SAV_IMAP_OPT_NOVALIDATE-CERT' => '/novalidate-cert',
        'PS_SAV_IMAP_OPT_TLS' => '/tls',
        'PS_SAV_IMAP_OPT_NOTLS' => '/notls',
    ];

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var CustomerMessageSyncImapRepository
     */
    private $syncImapRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        Context $context,
        ConfigurationInterface $configuration,
        CustomerMessageSyncImapRepository $syncImapRepository
    ) {
        $this->context = $context;
        $this->configuration = $configuration;
        $this->syncImapRepository = $syncImapRepository;
        $this->translator = $context->getTranslator();
    }

    public function handle(SyncCustomerServiceImapMailboxCommand $command): ImapSyncResult
    {
        $url = $this->configuration->get('PS_SAV_IMAP_URL');
        $port = $this->configuration->get('PS_SAV_IMAP_PORT');
        $user = $this->configuration->get('PS_SAV_IMAP_USER');
        $password = $this->configuration->get('PS_SAV_IMAP_PWD');

        if (!$url || !$port || !$user || !$password) {
            return new ImapSyncResult([
                $this->translator->trans('IMAP configuration is not correct', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        if (!function_exists('imap_open')) {
            return new ImapSyncResult([
                $this->translator->trans('IMAP is not installed on this server', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        $mbox = @imap_open('{' . $url . ':' . $port . $this->buildConnectionOptions() . '}', $user, $password);

        $errors = imap_errors();
        $mailboxErrors = is_array($errors) ? array_unique($errors) : [];
        $strErrors = implode(', ', $mailboxErrors);

        if (!$mbox) {
            if (empty($mailboxErrors)) {
                return new ImapSyncResult([
                    $this->translator->trans('Cannot connect to the mailbox', [], 'Admin.Orderscustomers.Notification'),
                ]);
            }

            // The first IMAP error is folded into the intro sentence so it
            // doesn't render as its own bare bullet; any further IMAP errors
            // are returned as their own array entries, one per alert line
            // (see the alert() macro: >1 message → one <li> each).
            $mailboxErrors[0] = $this->translator->trans(
                'Cannot connect to the mailbox: %error%',
                ['%error%' => $mailboxErrors[0]],
                'Admin.Orderscustomers.Notification'
            );

            return new ImapSyncResult(array_values($mailboxErrors));
        }

        $check = imap_check($mbox);
        if (!$check) {
            imap_close($mbox);

            return new ImapSyncResult([
                $this->translator->trans('Failed to get information about the current mailbox', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        if (0 === $check->Nmsgs) {
            imap_close($mbox);

            return new ImapSyncResult();
        }

        $messageErrors = [];
        $strErrorDelete = '';
        $overviews = imap_fetch_overview($mbox, "1:{$check->Nmsgs}", 0);

        foreach ($overviews as $overview) {
            $subject = $overview->subject ?? '';
            $md5 = md5($overview->date . $overview->from . $subject . $overview->msgno);

            if ($this->syncImapRepository->isAlreadyProcessed($md5)) {
                if ($this->configuration->get('PS_SAV_IMAP_DELETE_MSG') && !imap_delete($mbox, $overview->msgno)) {
                    $strErrorDelete = ', ' . $this->translator->trans('Fail to delete message', [], 'Admin.Orderscustomers.Notification');
                }

                continue;
            }

            $error = $this->processOverview($mbox, $overview, $subject);
            if (null !== $error) {
                $messageErrors[] = $error;
            }

            $this->syncImapRepository->markAsProcessed($md5);
        }

        imap_expunge($mbox);
        imap_close($mbox);

        $trailingError = $strErrors . $strErrorDelete;
        if (count($messageErrors) > 0) {
            return new ImapSyncResult('' !== $trailingError ? array_merge([$trailingError], $messageErrors) : $messageErrors);
        }

        if ('' !== $trailingError) {
            return new ImapSyncResult([$trailingError]);
        }

        return new ImapSyncResult();
    }

    /**
     * Returns an error message, or null when the message (if any) was processed successfully.
     *
     * @param resource|\IMAP\Connection $mbox
     */
    private function processOverview($mbox, object $overview, string $subject): ?string
    {
        preg_match('/#ct([0-9]*)/', $subject, $threadIdMatch);
        preg_match('/#tc([0-9-a-z-A-Z]*)/', $subject, $tokenMatch);
        $matchFound = isset($threadIdMatch[1], $tokenMatch[1]);

        $createNewThread = $this->configuration->get('PS_SAV_IMAP_CREATE_THREADS')
            && !$matchFound
            && false === strpos($subject, '[no_sync]');

        if (!$matchFound && !$createNewThread) {
            return null;
        }

        if ($createNewThread) {
            $customerThread = $this->createThreadForUnrecognizedSender($overview);
            if (null === $customerThread) {
                return $this->translator->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification');
            }
        } else {
            $customerThread = new CustomerThread((int) $threadIdMatch[1]);
            if (!Validate::isLoadedObject($customerThread) || $customerThread->token !== $tokenMatch[1]) {
                return null;
            }
        }

        return $this->createMessageFromOverview($mbox, $overview, $customerThread, $subject);
    }

    private function createThreadForUnrecognizedSender(object $overview): ?CustomerThread
    {
        $fromParsed = [];
        $from = $overview->from ?? null;

        if (!$from
            || (!preg_match('/<([a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+)>/', $from, $fromParsed)
                && !Validate::isEmail($from))
        ) {
            return null;
        }

        $senderEmail = $fromParsed[1] ?? $from;

        $contacts = Contact::getContacts($this->context->language->id);
        if (!$contacts) {
            return null;
        }

        $contactId = $contacts[0]['id_contact'];
        foreach ($contacts as $contact) {
            if (isset($overview->to) && false !== strpos($overview->to, $contact['email'])) {
                $contactId = $contact['id_contact'];

                break;
            }
        }

        $customer = new Customer();
        $matchingCustomer = $customer->getByEmail($senderEmail);

        $customerThread = new CustomerThread();
        if (isset($matchingCustomer->id)) {
            $customerThread->id_customer = $matchingCustomer->id;
        }
        $customerThread->email = $senderEmail;
        $customerThread->id_contact = $contactId;
        $customerThread->id_lang = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $customerThread->id_shop = $this->context->shop->id;
        $customerThread->status = CustomerThreadStatus::OPEN;
        $customerThread->token = Tools::passwdGen(12);
        $customerThread->add();

        return $customerThread;
    }

    /**
     * Returns an error message, or null on success.
     *
     * @param resource|\IMAP\Connection $mbox
     */
    private function createMessageFromOverview($mbox, object $overview, CustomerThread $customerThread, string $subject): ?string
    {
        $structure = imap_bodystruct($mbox, $overview->msgno, '1');
        if (0 === $structure->type) {
            $body = imap_fetchbody($mbox, $overview->msgno, '1');
        } elseif (1 === $structure->type) {
            $structure = imap_bodystruct($mbox, $overview->msgno, '1.1');
            $body = imap_fetchbody($mbox, $overview->msgno, '1.1');
        } else {
            return null;
        }

        switch ($structure->encoding) {
            case 3:
                $body = imap_base64($body);

                break;
            case 4:
                $body = imap_qprint($body);

                break;
        }

        $body = nl2br(iconv($this->getEncoding($structure), 'utf-8', $body));

        if ('' === $body) {
            return $this->translator->trans('The message body is empty, cannot import it.', [], 'Admin.Orderscustomers.Notification');
        }

        if (!Validate::isCleanHtml($body)) {
            return $this->translator->trans('Invalid message content for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification');
        }

        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerThread->id;
        $customerMessage->message = $body;

        try {
            $customerMessage->add();
        } catch (PrestaShopException) {
            return $this->translator->trans('The message content is not valid, cannot import it.', [], 'Admin.Orderscustomers.Notification');
        }

        return null;
    }

    private function buildConnectionOptions(): string
    {
        $options = '';
        foreach (self::OPTION_CONFIGURATION_KEYS as $configurationKey => $flag) {
            if ($this->configuration->get($configurationKey)) {
                $options .= $flag;
            }
        }

        return $options;
    }

    private function getEncoding(object $structure): string
    {
        foreach ($structure->parameters as $parameter) {
            if ('CHARSET' === strtoupper($parameter->attribute)) {
                return $parameter->value;
            }
        }

        return 'utf-8';
    }
}
