<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use Cache;
use CheckoutPersonalInformationStep;
use CheckoutProcess;
use CheckoutSession;
use Context;
use Customer;
use CustomerForm;
use CustomerLoginForm;
use Db;
use Hook;
use Module;
use PHPUnit\Framework\MockObject\MockObject;
use SubmitAccountHookTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * actionSubmitAccountBefore lets a module refuse an account creation, but a module that only
 * observes the hook returns nothing, which arrives as null in the results array. Such a module must
 * not block the checkout.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37103
 */
class CheckoutPersonalInformationStepHookTest extends KernelTestCase
{
    private const MODULE = 'submitaccounthooktest';

    /**
     * @var int
     */
    private $moduleId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $db = Db::getInstance();
        $idHook = (int) $db->getValue('SELECT id_hook FROM ' . _DB_PREFIX_ . "hook WHERE name = 'actionSubmitAccountBefore'");
        $this->assertGreaterThan(0, $idHook, 'the actionSubmitAccountBefore hook is not registered in this shop');

        // The fixture module is registered directly: Module::install() needs a translator that this
        // test case does not boot, and the hook execution path only reads these three tables.
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . "module (name, active, version) VALUES ('" . self::MODULE . "', 1, '1.0.0')");
        $this->moduleId = (int) $db->Insert_ID();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'module_shop (id_module, id_shop, enable_device) VALUES (' . $this->moduleId . ', 1, 7)');
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'hook_module (id_module, id_shop, id_hook, position) VALUES (' . $this->moduleId . ', 1, ' . $idHook . ', 1)');

        // The module class is only included when PrestaShop instantiates it, and the tests below
        // drive its return value through a static property.
        $this->assertNotFalse(Module::getInstanceByName(self::MODULE), 'the fixture module could not be loaded');

        Cache::clean('*');
        Hook::resetStaticCache();
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'hook_module WHERE id_module = ' . $this->moduleId);
        $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . $this->moduleId);
        $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'module WHERE id_module = ' . $this->moduleId);
        SubmitAccountHookTest::$hookReturnValue = null;

        Cache::clean('*');
        Hook::resetStaticCache();
        parent::tearDown();
    }

    /**
     * Control for the two tests below: without it, a module that never runs would make both of them
     * pass for the wrong reason.
     */
    public function testTheFixtureModuleIsActuallyCalled(): void
    {
        SubmitAccountHookTest::$hookReturnValue = 'called';

        $this->assertSame(
            [self::MODULE => 'called'],
            Hook::exec('actionSubmitAccountBefore', [], null, true)
        );
    }

    public function testAModuleThatReturnsNothingDoesNotBlockTheAccountCreation(): void
    {
        SubmitAccountHookTest::$hookReturnValue = null;

        $registerForm = $this->registerForm();
        $registerForm->expects($this->once())->method('submit')->willReturn(true);

        $step = $this->step($registerForm);
        $step->handleRequest(['submitCreate' => 1]);

        $this->assertTrue($step->isComplete());
    }

    public function testAModuleThatReturnsFalseStillBlocksTheAccountCreation(): void
    {
        SubmitAccountHookTest::$hookReturnValue = false;

        $registerForm = $this->registerForm();
        $registerForm->expects($this->never())->method('submit');

        $step = $this->step($registerForm);
        $step->handleRequest(['submitCreate' => 1]);

        $this->assertFalse($step->isComplete());
    }

    /**
     * @return CustomerForm&MockObject
     */
    private function registerForm()
    {
        $registerForm = $this->createMock(CustomerForm::class);
        $registerForm->method('fillWith')->willReturnSelf();
        $registerForm->method('fillFromCustomer')->willReturnSelf();

        return $registerForm;
    }

    /**
     * @param CustomerForm&MockObject $registerForm
     */
    private function step($registerForm): CheckoutPersonalInformationStep
    {
        $session = $this->createMock(CheckoutSession::class);
        $session->method('getCustomer')->willReturn(new Customer());

        $process = $this->createMock(CheckoutProcess::class);
        $process->method('getCheckoutSession')->willReturn($session);
        $process->method('setHasErrors')->willReturnSelf();
        // setNextStepAsCurrent() walks the sibling steps; this step is the only one under test.
        $process->method('getSteps')->willReturn([]);

        $step = new CheckoutPersonalInformationStep(
            Context::getContext(),
            self::getContainer()->get(TranslatorInterface::class),
            $this->createMock(CustomerLoginForm::class),
            $registerForm
        );
        $step->setCheckoutProcess($process);

        return $step;
    }
}
