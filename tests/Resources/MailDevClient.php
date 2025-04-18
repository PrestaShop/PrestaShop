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
