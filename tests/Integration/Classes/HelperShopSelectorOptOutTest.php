<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminController;
use Configuration as LegacyConfiguration;
use Context;
use Db;
use HelperShop;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * A back-office page that has nothing to do with multistore can opt out of the shop selector by
 * setting AdminController::$display_multishop_selector to false, so no shop context switch is
 * offered where switching would change nothing.
 */
class HelperShopSelectorOptOutTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'configuration'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        self::$kernel->getContainer()
            ->get('prestashop.adapter.legacy.configuration')
            ->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        // The selector is only rendered from the second shop on.
        $db = Db::getInstance();
        $db->insert('shop_group', [
            'name' => 'test_group_selector', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $db->insert('shop', [
            'id_shop_group' => (int) $db->Insert_ID(), 'name' => 'test_shop_selector', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        LegacyShop::resetStaticCache();

        // getRenderedShopList() builds the context-switch URL from the current request.
        $_SERVER['REQUEST_URI'] ??= '/admin-dev/index.php';
        $_SERVER['QUERY_STRING'] ??= '';
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    public function testSelectorIsRenderedByDefault(): void
    {
        $helper = $this->buildHelperForController(true);

        $this->assertSame('<selector />', $helper->getRenderedShopList());
        $this->assertTrue($helper->templateWasCreated, 'The selector template was never reached.');
    }

    public function testSelectorIsSkippedWhenTheControllerOptsOut(): void
    {
        $helper = $this->buildHelperForController(false);

        $this->assertSame('', $helper->getRenderedShopList());
        $this->assertFalse($helper->templateWasCreated, 'The selector template was rendered despite the opt-out.');
    }

    private function buildHelperForController(bool $displaySelector): RecordingHelperShop
    {
        self::bootKernel();

        $controller = new OptOutTestAdminController();
        $controller->display_multishop_selector = $displaySelector;
        Context::getContext()->controller = $controller;

        $helper = new RecordingHelperShop();
        $helper->context = Context::getContext();

        return $helper;
    }
}

/**
 * Renders a marker instead of the real Smarty template, which lives in the back-office theme and is
 * not resolvable from the front-office Smarty instance used by the integration kernel.
 */
class RecordingHelperShop extends HelperShop
{
    public bool $templateWasCreated = false;

    public function createTemplate($tpl_name)
    {
        $this->templateWasCreated = true;

        // A string resource keeps the real template object, so the helper's assign() and fetch() run unchanged.
        return $this->context->smarty->createTemplate('string:<selector />');
    }
}

/**
 * AdminController's constructor bootstraps the whole legacy controller stack; only the multistore
 * properties matter here.
 */
class OptOutTestAdminController extends AdminController
{
    public function __construct()
    {
    }
}
