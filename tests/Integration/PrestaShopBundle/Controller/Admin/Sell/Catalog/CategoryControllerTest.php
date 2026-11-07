<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Dispatcher;
use Link;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class CategoryControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const SHOP_ID = 1;
    private const LANGUAGE_ID = 1;
    private const HOME_CATEGORY_ID = 2;
    private const CLOTHES_CATEGORY_ID = 3;

    private RouterInterface $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client, ShopConstraint::shop(self::SHOP_ID));
        $this->router = $this->client->getContainer()->get('router');
        $this->client->catchExceptions(false);
        $this->client->disableReboot();
        $this->setUrlRewriting(false);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables([
            'admin_filter',
            'category',
            'category_group',
            'category_lang',
            'category_shop',
            'configuration',
            'employee_session',
            'log',
        ]);
        Dispatcher::$instance = null;
        parent::tearDown();
    }

    public function testCreateAndPreview(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_create'));
        $form = $crawler->selectButton('save-and-preview-button')->form([
            'category[name][' . self::LANGUAGE_ID . ']' => 'Preview category',
            'category[link_rewrite][' . self::LANGUAGE_ID . ']' => 'preview-category',
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString('open_preview=1', $this->client->getResponse()->headers->get('Location'));
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertSame('admin_categories_edit', $this->client->getRequest()->attributes->get('_route'));
        $this->assertSame('Preview category', $crawler->filter('#category_name_' . self::LANGUAGE_ID)->attr('value'));

        $categoryId = $this->client->getRequest()->attributes->get('categoryId');
        $previewUrl = $crawler->filter('.js-preview-url')->attr('data-preview-url');
        parse_str(parse_url($previewUrl, PHP_URL_QUERY), $parameters);
        $this->assertSame((string) $categoryId, $parameters['id_category']);
        $this->assertSame('category', $parameters['controller']);
    }

    #[DataProvider('getUrlRewritingSettings')]
    public function testEditAndPreviewUsesSavedUrl(bool $rewritingEnabled): void
    {
        $this->setUrlRewriting($rewritingEnabled);
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_edit', ['categoryId' => self::CLOTHES_CATEGORY_ID]));
        $form = $crawler->selectButton('save-and-preview-button')->form([
            'category[name][' . self::LANGUAGE_ID . ']' => 'Updated preview category',
            'category[link_rewrite][' . self::LANGUAGE_ID . ']' => 'updated-preview-category',
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertSame(
            $this->router->generate('admin_categories_edit', [
                'categoryId' => self::CLOTHES_CATEGORY_ID,
                'open_preview' => 1,
            ]),
            $this->client->getResponse()->headers->get('Location')
        );
        $crawler = $this->client->followRedirect();
        $this->assertSame('Updated preview category', $crawler->filter('#category_name_' . self::LANGUAGE_ID)->attr('value'));
        $this->assertSame('updated-preview-category', $crawler->filter('#category_link_rewrite_' . self::LANGUAGE_ID)->attr('value'));
        $this->assertStringNotContainsString('open_preview', $crawler->filter('form[name=category]')->attr('action'));

        $previewUrl = $crawler->filter('.js-preview-url')->attr('data-preview-url');
        if ($rewritingEnabled) {
            $this->assertStringEndsWith('/' . self::CLOTHES_CATEGORY_ID . '-updated-preview-category', $previewUrl);
        } else {
            parse_str(parse_url($previewUrl, PHP_URL_QUERY), $parameters);
            $this->assertSame((string) self::CLOTHES_CATEGORY_ID, $parameters['id_category']);
            $this->assertSame('category', $parameters['controller']);
        }

        $this->client->request('GET', $this->router->generate('admin_categories_preview', [
            'categoryId' => self::CLOTHES_CATEGORY_ID,
        ]));
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertSame($previewUrl, $this->client->getResponse()->headers->get('Location'));
    }

    public function testInvalidFormDoesNotRedirectToPreview(): void
    {
        $this->setUrlRewriting(true);
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_edit', [
            'categoryId' => self::CLOTHES_CATEGORY_ID,
            'open_preview' => 1,
        ]));
        $this->client->submit($crawler->selectButton('save-and-preview-button')->form([
            'category[name][' . self::LANGUAGE_ID . ']' => '',
            'category[link_rewrite][' . self::LANGUAGE_ID . ']' => 'unsaved-rewrite',
        ]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringNotContainsString('open_preview', $this->client->getRequest()->getUri());
        $this->assertGreaterThan(0, $this->client->getCrawler()->filter('.form-error-icon')->count());
        $this->assertSame('', $this->client->getCrawler()->filter('.js-preview-url')->attr('data-preview-url'));
    }

    public function testSaveReturnsToCategoryList(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_edit', ['categoryId' => self::CLOTHES_CATEGORY_ID]));
        $this->assertSame('', $crawler->filter('.js-preview-url')->attr('data-preview-url'));
        $this->client->submit($crawler->selectButton('save-button')->form());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertSame(
            $this->router->generate('admin_categories_index', ['categoryId' => self::HOME_CATEGORY_ID]),
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testGridPreviewLinkTargetsNewTab(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_index'));
        $previewUrl = $this->router->generate('admin_categories_preview', [
            'categoryId' => self::CLOTHES_CATEGORY_ID,
        ]);
        $preview = $crawler->filter('a[href="' . $previewUrl . '"]');
        $this->assertCount(1, $preview);
        $this->assertSame('_blank', $preview->attr('target'));
    }

    public function testRootCategoryFormDoesNotOfferSaveAndPreview(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_categories_create_root'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('button[name="save-and-preview"]'));
        $this->assertCount(1, $crawler->filter('#save-button'));
    }

    public static function getUrlRewritingSettings(): iterable
    {
        yield 'friendly URLs disabled' => [false];
        yield 'friendly URLs enabled' => [true];
    }

    private function setUrlRewriting(bool $enabled): void
    {
        $this->client->getContainer()->get('prestashop.adapter.legacy.configuration')->set(
            'PS_REWRITING_SETTINGS',
            $enabled,
            ShopConstraint::shop(self::SHOP_ID)
        );
        self::getMockedContext()->link = new Link('http://', 'http://');
        Dispatcher::$instance = null;
    }
}
