<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase\SymfonyIntegrationTestCase;

class SearchConfigurationCronTest extends SymfonyIntegrationTestCase
{
    private const CRON_TOKEN_START_POS = 34;
    private const CRON_TOKEN_LENGTH = 8;
    private const UNKNOWN_SHOP_ID = 999;

    public function testValidTokenRunsIndexation(): void
    {
        $this->client->request('GET', $this->cronUrl($this->validToken(), 1));

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('OK', $this->client->getResponse()->getContent());
    }

    public function testWrongTokenIsForbidden(): void
    {
        $this->client->request('GET', $this->cronUrl('wrong-token', 1));

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testUnknownShopIsBadRequest(): void
    {
        $this->client->request('GET', $this->cronUrl($this->validToken(), self::UNKNOWN_SHOP_ID));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Unknown shop', $this->client->getResponse()->getContent());
    }

    private function validToken(): string
    {
        return substr((string) self::getContainer()->getParameter('cookie_key'), self::CRON_TOKEN_START_POS, self::CRON_TOKEN_LENGTH);
    }

    private function cronUrl(string $token, int $shopId): string
    {
        return self::getContainer()->get('router')->generate('admin_search_indexation_cron', [
            'token' => $token,
            'id_shop' => $shopId,
        ]);
    }
}
