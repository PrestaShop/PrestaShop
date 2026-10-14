<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\MailTemplate;

use Cache;
use Hook;
use Mail;
use Module;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPreviewVariablesBuilder;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

/**
 * A module registering a placeholder through actionGetExtraMailTemplateVars saw it substituted in the
 * sent mail but left raw in the back-office preview, because the preview built its variables without
 * ever dispatching that hook.
 */
class MailPreviewVariablesBuilderTest extends KernelTestCase
{
    private const MODULE_NAME = 'ps_mailextravarstest';
    private const PLACEHOLDER = '{ps_mailextravarstest_placeholder}';
    private const VALUE = 'value injected by a module';

    private const TABLES_TO_RESTORE = [
        'module',
        'module_shop',
        'hook_module',
        'module_group',
        'authorization_role',
        'module_access',
    ];

    private ?ContextMocker $contextMocker = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        $this->contextMocker = (new ContextMocker())->mockContext();
    }

    protected function tearDown(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        if ($moduleManager->isInstalled(self::MODULE_NAME)) {
            $module = Module::getInstanceByName(self::MODULE_NAME);
            if ($module instanceof Module) {
                $module->uninstall();
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    /**
     * Guards the shared helper Mail::send() and the preview both go through.
     */
    public function testExtraTemplateVarsAreCollectedFromModules(): void
    {
        // Control: nothing is contributed while no module is listening.
        $this->assertSame([], Mail::getExtraTemplateVars('order_conf', [], 1));

        $this->installModule();

        $this->assertSame(
            [self::PLACEHOLDER => self::VALUE],
            Mail::getExtraTemplateVars('order_conf', [], 1)
        );
    }

    public function testPreviewVariablesContainTheModulePlaceholder(): void
    {
        $builder = self::getContainer()->get(MailPreviewVariablesBuilder::class);
        // Any layout other than the order ones: buildOrderVariables() returns early, so the test
        // exercises the hook rather than the demo order fixture.
        $layout = new Layout('account', '', '');

        // Control: the placeholder is absent while no module is listening, and the builder still works.
        $before = $builder->buildTemplateVariables($layout);
        $this->assertArrayHasKey('{shop_name}', $before);
        $this->assertArrayNotHasKey(self::PLACEHOLDER, $before);

        $this->installModule();

        $after = $builder->buildTemplateVariables($layout);
        $this->assertArrayHasKey(self::PLACEHOLDER, $after);
        $this->assertSame(self::VALUE, $after[self::PLACEHOLDER]);
    }

    private function installModule(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $this->assertTrue((bool) $moduleManager->install(self::MODULE_NAME));

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
    }
}
