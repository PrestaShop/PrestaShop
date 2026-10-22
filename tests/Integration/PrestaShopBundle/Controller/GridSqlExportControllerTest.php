<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

class GridSqlExportControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client);
    }

    public function testItReturnsTheRegeneratedSqlAsJsonForAnOptedInGrid(): void
    {
        $sql = $this->requestSql('prestashop.core.grid.factory.order');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsStringIgnoringCase('select', $sql);
        $this->assertStringContainsStringIgnoringCase('orders', $sql);
    }

    public function testTheRegeneratedSqlForAShopScopedGridCarriesTheShopScope(): void
    {
        // FeatureFilters extends ShopFilters, so the regenerated query must keep the shop scope
        // (this is what makes reusing the concrete Filters class necessary).
        $sql = $this->requestSql('prestashop.core.grid.grid_factory.feature');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsStringIgnoringCase('shop', $sql);
    }

    public function testItRejectsAGridThatDidNotOptIn(): void
    {
        // The customer grid does not implement SqlExportableGridDefinitionFactoryInterface.
        $this->requestSql('prestashop.core.grid.factory.customer');

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @param string $gridFactoryServiceId
     *
     * @return string the regenerated SQL returned by the endpoint (empty on non-2xx responses)
     */
    private function requestSql(string $gridFactoryServiceId): string
    {
        $url = $this->client->getContainer()->get('router')->generate('admin_common_grid_sql');

        $this->client->request(
            'POST',
            $url,
            ['gridFactoryServiceId' => $gridFactoryServiceId],
            [],
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
        );

        $content = $this->client->getResponse()->getContent();
        $decoded = json_decode((string) $content, true);

        return is_array($decoded) && isset($decoded['sql']) ? (string) $decoded['sql'] : '';
    }
}
