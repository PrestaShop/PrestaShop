<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Email;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Message;

/**
 * Sends MIME messages through PHP's mail() function.
 */
final class PhpMailTransport extends AbstractTransport
{
    /**
     * Returns the transport identifier.
     */
    public function __toString(): string
    {
        return 'php-mail://default';
    }

    /**
     * Passes the encoded message and its recipient headers to PHP's native mail transport.
     */
    protected function doSend(SentMessage $message): void
    {
        // Read the structured headers used by PHP to route native mail.
        $original = $message->getOriginalMessage();
        if (!$original instanceof Message) {
            throw new TransportException('PHP mail transport requires a structured MIME message.');
        }
        $headers = $original->getPreparedHeaders();
        $to = $headers->get('To')?->getBodyAsString() ?? '';
        $subject = $headers->get('Subject')?->getBodyAsString() ?? '';

        // Use the finalized MIME body and message ID, including any signatures and attachments.
        [$rawHeaders, $body] = explode("\r\n\r\n", $message->toString(), 2);
        $rawHeaders = preg_replace('/^(?:To|Subject):[^\r\n]*(?:\r\n[ \t][^\r\n]*)*\r\n?/mi', '', $rawHeaders . "\r\n");

        // Native mail routes copy recipients from headers; the mail server removes Bcc on delivery.
        if ($bcc = $original->getHeaders()->get('Bcc')) {
            $rawHeaders .= $bcc->toString();
        }

        // Use PHP's native Windows delivery without sendmail arguments.
        if ('\\' === DIRECTORY_SEPARATOR) {
            $sent = mail($to, $subject, $body, rtrim($rawHeaders, "\r\n"));
        } else {
            // Restrict the envelope sender to shell-safe ASCII characters for the native mail command.
            $sender = $message->getEnvelope()->getSender()->getAddress();
            if (!preg_match('/\A[a-zA-Z0-9@_.+-]+\z/', $sender)) {
                throw new TransportException('PHP mail transport cannot safely pass the envelope sender to the mail command.');
            }

            // Pass the envelope sender with -f so the mail server can set the Return-Path.
            $sent = mail($to, $subject, $body, rtrim($rawHeaders, "\r\n"), '-f' . $sender);
        }

        // Report native transport failures through Symfony Mailer's exception handling.
        if (!$sent) {
            throw new TransportException('PHP mail() failed to accept the message for delivery.');
        }
    }
}
