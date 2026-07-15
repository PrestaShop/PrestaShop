<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Contact;
use Context;
use Customer;
use CustomerMessage;
use CustomerThread;
use PrestaShop\PrestaShop\Adapter\CustomerService\Configuration\ImapConfiguration;
use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerMessageSyncImapRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidImapServerValidator;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\SyncCustomerServiceImapMailboxCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\SyncCustomerServiceImapMailboxHandlerInterface;
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
final class SyncCustomerServiceImapMailboxHandler implements SyncCustomerServiceImapMailboxHandlerInterface
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

    private readonly TranslatorInterface $translator;

    public function __construct(
        private readonly Context $context,
        private readonly ConfigurationInterface $configuration,
        private readonly ImapConfiguration $imapConfiguration,
        private readonly CustomerMessageSyncImapRepository $syncImapRepository,
    ) {
        $this->translator = $context->getTranslator();
    }

    public function handle(SyncCustomerServiceImapMailboxCommand $command): ImapSyncResult
    {
        // Single source of truth for "is the connection fully configured",
        // shared with the controller's "Run sync" button gating so the two
        // never drift into checking a different subset of fields.
        if (!$this->imapConfiguration->isConnectionComplete()) {
            return new ImapSyncResult([
                $this->translator->trans('IMAP configuration is not correct', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        $settings = $this->imapConfiguration->getConnectionSettings();
        $url = $settings['PS_SAV_IMAP_URL'];
        $port = $settings['PS_SAV_IMAP_PORT'];
        $user = $settings['PS_SAV_IMAP_USER'];
        $password = $settings['PS_SAV_IMAP_PWD'];

        if (!function_exists('imap_open')) {
            return new ImapSyncResult([
                $this->translator->trans('IMAP is not installed on this server', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        // Defense-in-depth: re-check the connection settings right before
        // building the mailbox string, regardless of the Form-time
        // `ValidImapServer` constraint on the options page — this settings
        // is read from configuration on every sync, so a value set through
        // any other path (direct DB write, module, future API endpoint)
        // still gets checked here before it can reach `imap_open()`.
        if (ValidImapServerValidator::containsSuspiciousPattern($url)
            || ValidImapServerValidator::containsSuspiciousPattern($port)
            || ValidImapServerValidator::containsSuspiciousPattern($user)
        ) {
            return new ImapSyncResult([
                $this->translator->trans('The IMAP configuration contains characters that are not allowed.', [], 'Admin.Orderscustomers.Notification'),
            ]);
        }

        $mbox = @imap_open('{' . $url . ':' . $port . $this->buildConnectionOptions($settings) . '}', $user, $password);

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

        // Compute both hashes for every message up front so a single query
        // can check them all at once. This used to issue one query PER
        // message, and since this sync fires on every Customer Service page
        // load, a mailbox with hundreds of already-processed messages meant
        // hundreds of round-trips on every admin page view.
        $overviewHashes = [];
        $allHashes = [];
        foreach ($overviews as $overview) {
            $subject = $overview->subject ?? '';
            // The Message-ID header is a stable identity for a given email.
            // The IMAP sequence number (msgno) is NOT: it shifts every time
            // imap_expunge() runs (called at the end of every sync below),
            // so keying the dedupe hash on it could reprocess/duplicate a
            // message the very next time the mailbox is synced.
            $messageId = $overview->message_id ?? '';
            $md5 = '' !== $messageId
                ? md5($messageId)
                : md5(($overview->date ?? '') . ($overview->from ?? '') . $subject);
            // The legacy controller included the unstable sequence number in
            // its hash. Check that value as well so mail already imported by
            // the legacy page is not imported a second time — though this
            // only holds up to the first imap_expunge() run by this handler,
            // since msgno (baked into the legacy hash) shifts on every
            // expunge afterwards. It's a best-effort, first-sync-only bridge
            // between the two hashing schemes, not a permanent one.
            $legacyMd5 = md5(($overview->date ?? '') . ($overview->from ?? '') . $subject . $overview->msgno);

            $overviewHashes[] = [$overview, $subject, $md5, $legacyMd5];
            $allHashes[] = $md5;
            $allHashes[] = $legacyMd5;
        }

        $processedHashes = array_flip($this->syncImapRepository->getAlreadyProcessedHashes(array_unique($allHashes)));

        foreach ($overviewHashes as [$overview, $subject, $md5, $legacyMd5]) {
            if (isset($processedHashes[$md5]) || isset($processedHashes[$legacyMd5])) {
                // Stabilize legacy entries immediately: their hash contains an
                // IMAP sequence number which may change after an expunge. The
                // old legacy-hash row is intentionally left in place rather
                // than deleted — one small row per migrated message is
                // harmless to keep once superseded by the new hash.
                if (!isset($processedHashes[$md5])) {
                    $this->syncImapRepository->markAsProcessed($md5);
                    $processedHashes[$md5] = true;
                }

                if (($settings['PS_SAV_IMAP_DELETE_MSG'] ?? false) && !imap_delete($mbox, $overview->msgno)) {
                    $strErrorDelete = ', ' . $this->translator->trans('Fail to delete message', [], 'Admin.Orderscustomers.Notification');
                }

                continue;
            }

            $processingResult = $this->processOverview($mbox, $overview, $subject, $settings);
            if (null !== $processingResult->getError()) {
                $messageErrors[] = $processingResult->getError();
            }

            if ($processingResult->shouldMarkAsProcessed()) {
                $this->syncImapRepository->markAsProcessed($md5);
                $processedHashes[$md5] = true;
            }
        }

        imap_expunge($mbox);
        imap_close($mbox);

        $trailingError = '' !== $strErrors
            ? $this->translator->trans('IMAP warning: %error%', ['%error%' => $strErrors], 'Admin.Orderscustomers.Notification') . $strErrorDelete
            : $strErrorDelete;

        if (count($messageErrors) > 0) {
            return new ImapSyncResult('' !== $trailingError ? array_merge([$trailingError], $messageErrors) : $messageErrors);
        }

        if ('' !== $trailingError) {
            return new ImapSyncResult([$trailingError]);
        }

        return new ImapSyncResult();
    }

    /**
     * @param resource|\IMAP\Connection $mbox
     * @param array<string, string|bool> $settings
     */
    private function processOverview($mbox, object $overview, string $subject, array $settings): ImapMessageProcessingResult
    {
        preg_match('/#ct([0-9]*)/', $subject, $threadIdMatch);
        preg_match('/#tc([0-9-a-z-A-Z]*)/', $subject, $tokenMatch);
        $matchFound = isset($threadIdMatch[1], $tokenMatch[1]);

        $createNewThread = ($settings['PS_SAV_IMAP_CREATE_THREADS'] ?? false)
            && !$matchFound
            && false === strpos($subject, '[no_sync]');

        if (!$matchFound && !$createNewThread) {
            return ImapMessageProcessingResult::processed();
        }

        $newThreadCreated = false;
        if ($createNewThread) {
            $senderEmail = $this->parseSenderEmail($overview);
            if (null === $senderEmail) {
                // The sender's email is a fixed property of this message: it
                // will never become parseable on a later sync, so this is
                // marked processed (not retried) even though it failed.
                return ImapMessageProcessingResult::processed(
                    $this->translator->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification')
                );
            }

            $customerThread = $this->createThreadForUnrecognizedSender($senderEmail, $overview);
            if (null === $customerThread) {
                return ImapMessageProcessingResult::failed(
                    $this->translator->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification')
                );
            }
            $newThreadCreated = true;
        } else {
            $customerThread = new CustomerThread((int) $threadIdMatch[1]);
            if (!Validate::isLoadedObject($customerThread) || $customerThread->token !== $tokenMatch[1]) {
                return ImapMessageProcessingResult::processed();
            }
        }

        $processingResult = $this->createMessageFromOverview($mbox, $overview, $customerThread, $subject);
        // Keyed on getError(), not shouldMarkAsProcessed(): a thread created
        // for this attempt is orphaned (no message ever got attached to it)
        // whether the failure is transient (retried) or permanent (marked
        // processed and never retried) — either way it must not be left
        // behind, otherwise a transient failure would also leave a new
        // empty thread behind on every retry.
        if ($newThreadCreated && null !== $processingResult->getError()) {
            try {
                $customerThread->delete();
            } catch (PrestaShopException) {
                // Keep the original processing failure as the sync result.
            }
        }

        return $processingResult;
    }

    private function parseSenderEmail(object $overview): ?string
    {
        $fromParsed = [];
        $from = $overview->from ?? null;

        if (!$from
            || (!preg_match('/<([a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+)>/', $from, $fromParsed)
                && !Validate::isEmail($from))
        ) {
            return null;
        }

        return $fromParsed[1] ?? $from;
    }

    private function createThreadForUnrecognizedSender(string $senderEmail, object $overview): ?CustomerThread
    {
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

        try {
            $customerThread->add();
        } catch (PrestaShopException) {
            return null;
        }

        return $customerThread;
    }

    /**
     * @param resource|\IMAP\Connection $mbox
     */
    private function createMessageFromOverview($mbox, object $overview, CustomerThread $customerThread, string $subject): ImapMessageProcessingResult
    {
        $structure = imap_bodystruct($mbox, $overview->msgno, '1');
        if (false === $structure) {
            // A read failure at the protocol level can be transient (a
            // network/server hiccup); retry on the next sync.
            return ImapMessageProcessingResult::failed(
                $this->translator->trans('Could not read the message structure for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
            );
        }

        if (0 === $structure->type) {
            $body = imap_fetchbody($mbox, $overview->msgno, '1');
        } elseif (1 === $structure->type) {
            $structure = imap_bodystruct($mbox, $overview->msgno, '1.1');
            if (false === $structure) {
                return ImapMessageProcessingResult::failed(
                    $this->translator->trans('Could not read the message structure for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
                );
            }
            $body = imap_fetchbody($mbox, $overview->msgno, '1.1');
        } else {
            // The message's top-level MIME type (anything but TEXT or
            // MULTIPART) is a fixed property of this email: it will never
            // become one this handler supports on a later sync, so this is
            // marked processed (not retried) even though it failed.
            return ImapMessageProcessingResult::processed(
                $this->translator->trans('Unsupported message format for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
            );
        }

        if (false === $body) {
            // Same as the imap_bodystruct() reads above: can be a transient
            // protocol-level failure, so retry on the next sync.
            return ImapMessageProcessingResult::failed(
                $this->translator->trans('Could not read the message body for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
            );
        }

        switch ($structure->encoding) {
            case 3:
                $body = imap_base64($body);

                break;
            case 4:
                $body = imap_qprint($body);

                break;
        }

        // iconv() returns false on illegal/mismatched charset bytes, which
        // real-world mail regularly contains. Under strict_types, passing
        // that false straight into nl2br(string) throws a TypeError that
        // would otherwise abort the whole sync (and, since this runs on
        // every Customer Service page load, break the listing page until
        // the offending message is purged from the mailbox). This and the
        // two checks below are all fixed properties of this message's
        // charset/body bytes: if they fail, they will fail identically on
        // every later sync, so all three are marked processed (not
        // retried) even though they failed.
        $decodedBody = iconv($this->getEncoding($structure), 'utf-8//IGNORE', $body);
        if (false === $decodedBody) {
            return ImapMessageProcessingResult::processed(
                $this->translator->trans('Invalid message encoding for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
            );
        }

        $body = nl2br($decodedBody);

        if ('' === $body) {
            return ImapMessageProcessingResult::processed(
                $this->translator->trans('The message body is empty, cannot import it.', [], 'Admin.Orderscustomers.Notification')
            );
        }

        if (!Validate::isCleanHtml($body)) {
            return ImapMessageProcessingResult::processed(
                $this->translator->trans('Invalid message content for subject: %subject%', ['%subject%' => $subject], 'Admin.Orderscustomers.Notification')
            );
        }

        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerThread->id;
        $customerMessage->message = $body;

        try {
            $customerMessage->add();
        } catch (PrestaShopException) {
            // A validation failure against this same, fixed message content
            // (e.g. length) will recur identically on every later sync.
            return ImapMessageProcessingResult::processed(
                $this->translator->trans('The message content is not valid, cannot import it.', [], 'Admin.Orderscustomers.Notification')
            );
        }

        return ImapMessageProcessingResult::processed();
    }

    /**
     * @param array<string, string|bool> $settings
     */
    private function buildConnectionOptions(array $settings): string
    {
        // /norsh is always forced, regardless of the PS_SAV_IMAP_OPT_NORSH
        // toggle: it disables the rsh/ssh preauthentication mechanism that
        // some IMAP c-client builds shell out to, which is the actual code
        // path CVE-2018-19518-class imap_open() RCEs exploit via a crafted
        // host/user string (see ValidImapServerValidator). No legitimate
        // modern IMAP server needs rsh/ssh preauth, so this trades an
        // obsolete, unauthenticated legacy transport for closing off the
        // vulnerability at its root rather than only filtering input to it.
        $options = '/norsh';
        foreach (self::OPTION_CONFIGURATION_KEYS as $configurationKey => $flag) {
            if ('/norsh' === $flag) {
                continue;
            }

            if ($settings[$configurationKey] ?? false) {
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
