<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Contact;
use Customer;
use CustomerMessage;
use CustomerThread;
use Db;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\SyncImapMessagesCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\SyncImapMessagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\ImapSyncResult;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Connects to the IMAP mailbox configured under `Sell > Customer Service >
 * Options` and imports every new message: subjects matching the legacy
 * `#ct{thread}#tc{token}` pattern are appended to the existing thread,
 * unrecognised mails (with `PS_SAV_IMAP_CREATE_THREADS` enabled) become
 * new threads assigned to the matching contact category.
 *
 * Direct port of `AdminCustomerThreadsController::syncImap()`. Each
 * imported message is fingerprinted (md5 of date + from + subject + msgno)
 * in `customer_message_sync_imap`, so a repeat pass over the same mailbox
 * is idempotent. When `PS_SAV_IMAP_DELETE_MSG` is enabled, already-imported
 * messages get `imap_delete`d to keep the mailbox lean.
 *
 * @internal
 */
#[AsCommandHandler]
final class SyncImapMessagesHandler implements SyncImapMessagesHandlerInterface
{
    private const IMAP_OPTION_FLAGS = [
        'PS_SAV_IMAP_OPT_POP3' => '/pop3',
        'PS_SAV_IMAP_OPT_NORSH' => '/norsh',
        'PS_SAV_IMAP_OPT_SSL' => '/ssl',
        'PS_SAV_IMAP_OPT_VALIDATE-CERT' => '/validate-cert',
        'PS_SAV_IMAP_OPT_NOVALIDATE-CERT' => '/novalidate-cert',
        'PS_SAV_IMAP_OPT_TLS' => '/tls',
        'PS_SAV_IMAP_OPT_NOTLS' => '/notls',
    ];

    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        private readonly TranslatorInterface $translator,
        private readonly string $dbPrefix,
    ) {
    }

    public function handle(SyncImapMessagesCommand $command): ImapSyncResult
    {
        $url = (string) $this->configuration->get('PS_SAV_IMAP_URL');
        $port = (string) $this->configuration->get('PS_SAV_IMAP_PORT');
        $user = (string) $this->configuration->get('PS_SAV_IMAP_USER');
        $password = (string) $this->configuration->get('PS_SAV_IMAP_PWD');

        if ('' === $url || '' === $port || '' === $user || '' === $password) {
            return new ImapSyncResult(['IMAP configuration is not correct']);
        }

        if (!function_exists('imap_open')) {
            return new ImapSyncResult(['imap is not installed on this server']);
        }

        $mailbox = @imap_open(
            sprintf('{%s:%s%s}', $url, $port, $this->buildImapOptionString()),
            $user,
            $password,
        );

        $connectionErrors = $this->collectConnectionErrors();

        if (!$mailbox) {
            return new ImapSyncResult([sprintf('Cannot connect to the mailbox :<br />%s', $connectionErrors)]);
        }

        $check = imap_check($mailbox);
        if (!$check) {
            imap_close($mailbox);

            return new ImapSyncResult(['Fail to get information about the current mailbox']);
        }

        if (0 === $check->Nmsgs) {
            imap_close($mailbox);

            return new ImapSyncResult(['NO message to sync']);
        }

        $deleteAfterSync = (bool) $this->configuration->get('PS_SAV_IMAP_DELETE_MSG');
        $createUnrecognised = (bool) $this->configuration->get('PS_SAV_IMAP_CREATE_THREADS');

        $overviews = imap_fetch_overview($mailbox, sprintf('1:%d', $check->Nmsgs), 0);
        $messageErrors = [];
        $deleteErrors = '';

        foreach ($overviews as $overview) {
            $subject = $overview->subject ?? '';
            $md5 = md5($overview->date . $overview->from . $subject . $overview->msgno);

            if ($this->isAlreadyImported($md5)) {
                if ($deleteAfterSync && !imap_delete($mailbox, $overview->msgno)) {
                    $deleteErrors = ', Fail to delete message';
                }

                continue;
            }

            $this->importOverview(
                $mailbox,
                $overview,
                $subject,
                $createUnrecognised,
                $messageErrors,
            );

            Db::getInstance()->execute(
                'INSERT INTO `' . $this->dbPrefix . 'customer_message_sync_imap` (`md5_header`) VALUES (\'' . pSQL($md5) . '\')'
            );
        }

        imap_expunge($mailbox);
        imap_close($mailbox);

        $aggregated = array_filter([trim($connectionErrors . $deleteErrors, ', ')]);

        return new ImapSyncResult(array_values(array_merge($aggregated, $messageErrors)));
    }

    /**
     * Builds the `/pop3/ssl/...` option string appended to the IMAP mailbox URL.
     */
    private function buildImapOptionString(): string
    {
        $options = '';
        foreach (self::IMAP_OPTION_FLAGS as $configurationKey => $optionFlag) {
            if ($this->configuration->get($configurationKey)) {
                $options .= $optionFlag;
            }
        }

        return $options;
    }

    /**
     * Returns a single string aggregating the connection errors reported by
     * `imap_errors()`. Empty when there is nothing to report.
     */
    private function collectConnectionErrors(): string
    {
        $errors = imap_errors();
        if (!is_array($errors) || [] === $errors) {
            return '';
        }

        return rtrim(implode(', ', array_unique($errors)), ', ');
    }

    private function isAlreadyImported(string $md5): bool
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT `md5_header` FROM `' . $this->dbPrefix . 'customer_message_sync_imap` WHERE `md5_header` = \'' . pSQL($md5) . '\''
        );
    }

    /**
     * Imports a single mailbox overview: either appends to an existing thread
     * (when the subject contains the legacy `#ct{id}#tc{token}` marker) or
     * creates a new one (when `PS_SAV_IMAP_CREATE_THREADS` is enabled). Errors
     * are pushed into `$messageErrors` for the caller to surface.
     *
     * @param array<int, string> $messageErrors
     */
    private function importOverview(
        $mailbox,
        object $overview,
        string $subject,
        bool $createUnrecognised,
        array &$messageErrors,
    ): void {
        preg_match('/\#ct([0-9]*)/', $subject, $threadMatch);
        preg_match('/\#tc([0-9-a-z-A-Z]*)/', $subject, $tokenMatch);
        $matchedExisting = isset($threadMatch[1], $tokenMatch[1]);

        $shouldCreateNew = $createUnrecognised && !$matchedExisting && false === strpos($subject, '[no_sync]');

        if (!$matchedExisting && !$shouldCreateNew) {
            return;
        }

        $thread = $shouldCreateNew
            ? $this->createThreadFromOverview($overview, $messageErrors)
            : new CustomerThread((int) $threadMatch[1]);

        if (null === $thread) {
            return;
        }

        if (!Validate::isLoadedObject($thread)) {
            return;
        }

        if (!$shouldCreateNew && (!isset($tokenMatch[1]) || $thread->token !== $tokenMatch[1])) {
            return;
        }

        $this->appendMessage($mailbox, $overview, $thread, $subject, $messageErrors);
    }

    /**
     * @param array<int, string> $messageErrors
     */
    private function createThreadFromOverview(object $overview, array &$messageErrors): ?CustomerThread
    {
        $fromMatch = [];
        if (!isset($overview->from)
            || (!preg_match(
                '/<([a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+)>/',
                $overview->from,
                $fromMatch,
            )
            && !Validate::isEmail($overview->from))) {
            $messageErrors[] = $this->translator->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification');

            return null;
        }

        $fromEmail = $fromMatch[1] ?? $overview->from;

        $contacts = Contact::getContacts($this->languageContext->getId());
        if (!$contacts) {
            return null;
        }

        $idContact = (int) $contacts[0]['id_contact'];
        if (isset($overview->to)) {
            foreach ($contacts as $contact) {
                if (false !== strpos((string) $overview->to, (string) $contact['email'])) {
                    $idContact = (int) $contact['id_contact'];
                }
            }
        }

        $customer = (new Customer())->getByEmail($fromEmail);

        $thread = new CustomerThread();
        if (isset($customer->id)) {
            $thread->id_customer = (int) $customer->id;
        }
        $thread->email = $fromEmail;
        $thread->id_contact = $idContact;
        $thread->id_lang = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $thread->id_shop = $this->shopContext->getId();
        $thread->status = 'open';
        $thread->token = Tools::passwdGen(12);
        $thread->add();

        return $thread;
    }

    /**
     * Pulls the body part of the overview and appends it as a customer message
     * to the resolved thread. Encoding-aware (handles base64 + quoted-printable
     * + arbitrary charsets via iconv).
     *
     * @param array<int, string> $messageErrors
     */
    private function appendMessage(
        $mailbox,
        object $overview,
        CustomerThread $thread,
        string $subject,
        array &$messageErrors,
    ): void {
        $structure = imap_bodystruct($mailbox, $overview->msgno, '1');
        if (0 === $structure->type) {
            $body = imap_fetchbody($mailbox, $overview->msgno, '1');
        } elseif (1 === $structure->type) {
            $structure = imap_bodystruct($mailbox, $overview->msgno, '1.1');
            $body = imap_fetchbody($mailbox, $overview->msgno, '1.1');
        } else {
            return;
        }

        $body = match ($structure->encoding) {
            3 => imap_base64($body),
            4 => imap_qprint($body),
            default => $body,
        };

        $body = nl2br((string) iconv($this->resolveEncoding($structure), 'utf-8', $body));

        if ('' === $body) {
            $messageErrors[] = $this->translator->trans('The message body is empty, cannot import it.', [], 'Admin.Orderscustomers.Notification');

            return;
        }

        if (!Validate::isCleanHtml($body)) {
            $messageErrors[] = $this->translator->trans('Invalid message content for subject: %s', [$subject], 'Admin.Orderscustomers.Notification');

            return;
        }

        $message = new CustomerMessage();
        $message->id_customer_thread = (int) $thread->id;
        $message->message = $body;

        try {
            $message->add();
        } catch (PrestaShopException $e) {
            $messageErrors[] = $this->translator->trans('The message content is not valid, cannot import it.', [], 'Admin.Orderscustomers.Notification');
        }
    }

    private function resolveEncoding(object $structure): string
    {
        foreach ($structure->parameters ?? [] as $parameter) {
            if ('CHARSET' === strtoupper($parameter->attribute)) {
                return (string) $parameter->value;
            }
        }

        return 'utf-8';
    }
}
