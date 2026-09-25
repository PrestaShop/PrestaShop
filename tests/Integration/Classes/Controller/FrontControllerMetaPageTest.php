<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Configuration;
use Context;
use Controller;
use Db;
use FrontController;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Tests\Resources\DatabaseDump;

/**
 * A controller tells the theme its page name and the meta table its php_self, and those two are not
 * always the same word: the order page is "checkout" for the theme and "order" in the table. The
 * meta lookup has to reach the row the merchant actually configured.
 */
class FrontControllerMetaPageTest extends TestCase
{
    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['meta', 'meta_lang']);

        parent::tearDown();
    }

    public function testAControllerConfiguredUnderItsPhpSelfFindsThatPage(): void
    {
        $this->assertSame('order', $this->resolveMetaPage('checkout', 'order'));
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

    public function testTheOrderPageIsRegisteredUnderOrderAndNotCheckout(): void
    {
        $prefix = _DB_PREFIX_;

        $this->assertSame('1', (string) Db::getInstance()->getValue("SELECT COUNT(*) FROM {$prefix}meta WHERE page = 'order'"));
        $this->assertSame('0', (string) Db::getInstance()->getValue("SELECT COUNT(*) FROM {$prefix}meta WHERE page = 'checkout'"));
    }
}
