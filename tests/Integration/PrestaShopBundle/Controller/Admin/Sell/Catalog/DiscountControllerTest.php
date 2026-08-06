<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

class DiscountControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    protected RouterInterface $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * When the create page is accessed without a discount type (e.g. inside the iframe modal
     * opened from the order creation page) the discount type selection must be displayed first.
     */
    public function testCreateWithoutTypeDisplaysTypeSelection(): void
    {
        $createUrl = $this->router->generate('admin_discounts_create', [
            'liteDisplaying' => 1,
            'submitFormAjax' => 1,
        ]);

        $this->client->catchExceptions(false);
        $crawler = $this->client->request('GET', $createUrl);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful(), (string) $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('form[name=discount_type_selector]'));
    }

    /**
     * Submitting the discount type selection must redirect to the typed create page and keep
     * the iframe modal parameters so the creation funnel stays in lite display.
     */
    public function testTypeSelectionRedirectsToTypedCreatePageKeepingModalParameters(): void
    {
        $createUrl = $this->router->generate('admin_discounts_create', [
            'liteDisplaying' => 1,
            'submitFormAjax' => 1,
        ]);

        $this->client->catchExceptions(false);
        $crawler = $this->client->request('GET', $createUrl);

        $form = $crawler->filter('form[name=discount_type_selector]')->form();
        $form['discount_type_selector[discount_type_selector]'] = DiscountType::CART_LEVEL;
        $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());

        $location = parse_url((string) $response->headers->get('location'));
        $locationParameters = [];
        parse_str($location['query'] ?? '', $locationParameters);
        unset($locationParameters['_token']);

        $this->assertStringEndsWith('/sell/catalog/discounts/new/' . DiscountType::CART_LEVEL, $location['path']);
        $this->assertEquals(['liteDisplaying' => '1', 'submitFormAjax' => '1'], $locationParameters);

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('form[name=discount]'));
    }
}
