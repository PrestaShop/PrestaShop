<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use AdminSearchController;
use Db;
use Symfony\Component\HttpFoundation\Response;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

class SearchConfigurationCronTest extends SymfonyIntegrationTestCase
{
    private const UNKNOWN_SHOP_ID = 999;

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['product_shop', 'search_index', 'search_word']);

        parent::tearDown();
    }

    public function testValidTokenRunsIndexation(): void
    {
        Db::getInstance()->update('product_shop', ['indexed' => 0], 'id_shop = 1');

        $this->client->request('GET', $this->cronUrl($this->validToken(), 1));

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('OK', $this->client->getResponse()->getContent());
        $this->assertGreaterThan(0, (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_shop = 1 AND indexed = 1'));
    }

    public function testValidTokenWithoutShopRunsIndexationForAllShops(): void
    {
        $this->client->request('GET', self::getContainer()->get('router')->generate('admin_search_indexation_cron', [
            'token' => $this->validToken(),
        ]));

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
        return substr((string) self::getContainer()->getParameter('cookie_key'), AdminSearchController::TOKEN_CHECK_START_POS, AdminSearchController::TOKEN_CHECK_LENGTH);
    }

    private function cronUrl(string $token, int $shopId): string
    {
        return self::getContainer()->get('router')->generate('admin_search_indexation_cron', [
            'token' => $token,
            'id_shop' => $shopId,
        ]);
    }
}
