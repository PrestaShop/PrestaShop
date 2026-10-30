<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Resources;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * MailDevClient is used in tests to check the emails sent that are caught and stored locally by maildev.
 * For it to ork you need to run a local maildev server by running this command:
 *     docker run -p 1080:1080 -p 1025:1025 maildev/maildev
 */
class MailDevClient
{
    public function __construct(
        private readonly string $mailDevHost,
        private readonly int $mailDevPort,
        private HttpClientInterface $client,
    ) {
    }

    public function getAllEmails(): array
    {
        $response = $this->client->request('GET', $this->buildUrl('/email'));
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to request MailDev, expected a server accessible at ' . $this->buildUrl(''));
        }

        return json_decode($response->getContent(), true);
    }

    /**
     * The SMTP server accepts an email before maildev lists it, so reading the list right after
     * sending can miss it. This polls the list until it holds at least $expectedCount emails or
     * the timeout is reached, and returns the last list read.
     */
    public function waitForEmails(int $expectedCount, int $timeoutMs = 5000, int $intervalMs = 200): array
    {
        $deadline = microtime(true) + $timeoutMs / 1000;
        while (true) {
            $emails = $this->getAllEmails();
            if (count($emails) >= $expectedCount || microtime(true) >= $deadline) {
                return $emails;
            }
            usleep($intervalMs * 1000);
        }
    }

    public function getEmail(int $emailId): array
    {
        $response = $this->client->request('GET', $this->buildUrl('/email/' . $emailId));
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to request MailDev, expected a server accessible at ' . $this->buildUrl(''));
        }

        return json_decode($response->getContent(), true);
    }

    public function deleteAllEmails(): void
    {
        $response = $this->client->request('DELETE', $this->buildUrl('/email/all'));
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to request MailDev, expected a server accessible at ' . $this->buildUrl(''));
        }
    }

    private function buildUrl(string $endpoint): string
    {
        $host = str_starts_with('http://', $this->mailDevHost) ? $this->mailDevHost : 'http://' . $this->mailDevHost;
        $host .= ':' . $this->mailDevPort;

        return $host . $endpoint;
    }
}
