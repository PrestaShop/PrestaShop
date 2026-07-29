<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\controller;

use Configuration;
use Context;
use Controller;
use Db;
use FrontController;
use Language;
use Meta;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Tests\Resources\DatabaseDump;

class FrontControllerMetaPageTest extends TestCase
{
    private ?Language $languageBackup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->languageBackup = Context::getContext()->language;
    }

    protected function tearDown(): void
    {
        Context::getContext()->language = $this->languageBackup;
        DatabaseDump::restoreTables(['meta', 'meta_lang']);

        parent::tearDown();
    }

    public function testAControllerConfiguredUnderItsPhpSelfFindsThatPage(): void
    {
        $langId = (int) Configuration::get('PS_LANG_DEFAULT');
        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'meta_lang ml INNER JOIN ' . _DB_PREFIX_ . "meta m ON m.id_meta = ml.id_meta
             SET ml.title = 'Order page title' WHERE m.page = 'order' AND ml.id_lang = " . $langId
        );

        $metaPage = $this->resolveMetaPage('checkout', 'order');

        $this->assertSame('order', $metaPage);
        $this->assertStringContainsString('Order page title', Meta::getMetaTags($langId, $metaPage)['meta_title']);
    }

    public function testAPageThatHasItsOwnRowIsLeftAlone(): void
    {
        $this->assertSame('cart', $this->resolveMetaPage('cart', 'order'));
    }

    public function testAControllerWithoutAPhpSelfIsLeftAlone(): void
    {
        $this->assertSame('checkout', $this->resolveMetaPage('checkout', ''));
    }

    private function resolveMetaPage(string $pageName, string $phpSelf): string
    {
        $controller = new class() extends FrontController {
            public function __construct()
            {
            }
        };
        $controller->php_self = $phpSelf;

        $context = Context::getContext();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $contextProperty = new ReflectionProperty(Controller::class, 'context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($controller, $context);

        $method = new ReflectionMethod(FrontController::class, 'getMetaPageName');
        $method->setAccessible(true);

        return (string) $method->invoke($controller, $pageName);
    }
}
