<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Shop;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class ShopControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const SHOP_NAME = 'Shop FR/BE 43635';

    private RouterInterface $router;

    private string $originalShopName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');

        $shop = new Shop(1);
        $this->originalShopName = $shop->name;
        $shop->name = self::SHOP_NAME;
        $shop->update();
    }

    protected function tearDown(): void
    {
        $shop = new Shop(1);
        $shop->name = $this->originalShopName;
        $shop->update();
        parent::tearDown();
    }

    public function testSearchFindsTheTermSentAsQueryParameter(): void
    {
        $this->assertSame(
            [self::SHOP_NAME],
            $this->search($this->router->generate('admin_shops_search', ['query' => 'FR/BE 43635']))
        );
    }

    public function testSearchStillReadsTheTermFromThePath(): void
    {
        $this->assertSame(
            [self::SHOP_NAME],
            $this->search($this->router->generate('admin_shops_search', ['searchTerm' => 'Shop FR']))
        );
    }

    public function testSearchWithoutTermFindsNothing(): void
    {
        $this->client->request('GET', $this->router->generate('admin_shops_search'));

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return string[]
     */
    private function search(string $url): array
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        return array_column(json_decode((string) $response->getContent(), true), 'name');
    }
}
