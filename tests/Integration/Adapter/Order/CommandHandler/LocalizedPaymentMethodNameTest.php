<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\CommandHandler;

use Context;
use Language;
use PaymentModule;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\CommandHandler\AddOrderFromBackOfficeHandler;
use ReflectionClass;
use ReflectionMethod;

/**
 * A module translates its displayName once, in its constructor. Building the back office order form
 * already asks Module::getInstanceByName() for the payment modules to fill its dropdown, so the shared
 * instance the handler receives is translated for the employee.
 *
 * The name the handler passes to validateOrder() is stored on the order and shown to the customer on the
 * invoice and the confirmation email, so it has to follow the order's language instead.
 */
class LocalizedPaymentMethodNameTest extends TestCase
{
    private ?Language $originalLanguage = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalLanguage = Context::getContext()->language;
    }

    protected function tearDown(): void
    {
        Context::getContext()->language = $this->originalLanguage;

        parent::tearDown();
    }

    public function testItIgnoresTheNameTheModuleWasBuiltWith(): void
    {
        [$employeeLanguage, $orderLanguage] = $this->twoDistinctLanguages();

        // The order form built the module while the employee's language was active.
        Context::getContext()->language = $employeeLanguage;
        $moduleAsTheFormBuiltIt = new LanguageStampedPaymentModule();
        self::assertSame(
            'name-in-' . $employeeLanguage->iso_code,
            $moduleAsTheFormBuiltIt->displayName,
            'the fixture should record the language it was constructed under'
        );

        // The handler switches the context to the cart's language before validating the order.
        Context::getContext()->language = $orderLanguage;

        self::assertSame(
            'name-in-' . $orderLanguage->iso_code,
            $this->resolveNameFor($moduleAsTheFormBuiltIt),
            'the order kept the payment method name translated for the employee'
        );
    }

    /**
     * When nothing changed the context, the answer must stay the same rather than drift.
     */
    public function testItKeepsTheNameWhenTheLanguageDidNotChange(): void
    {
        [$employeeLanguage] = $this->twoDistinctLanguages();

        Context::getContext()->language = $employeeLanguage;
        $module = new LanguageStampedPaymentModule();

        self::assertSame('name-in-' . $employeeLanguage->iso_code, $this->resolveNameFor($module));
    }

    /**
     * A module that exposes no display name at all must not turn the order's payment method into an
     * empty string.
     */
    public function testItFallsBackWhenTheFreshInstanceHasNoName(): void
    {
        // Whatever the form handed over already carries a name; a rebuilt instance of this class does not.
        $moduleAsTheFormBuiltIt = new NamelessPaymentModule();
        $moduleAsTheFormBuiltIt->displayName = 'Wire payment';

        self::assertSame('Wire payment', $this->resolveNameFor($moduleAsTheFormBuiltIt));
    }

    private function resolveNameFor(PaymentModule $module): string
    {
        $handler = (new ReflectionClass(AddOrderFromBackOfficeHandler::class))->newInstanceWithoutConstructor();

        $method = new ReflectionMethod(AddOrderFromBackOfficeHandler::class, 'getLocalizedPaymentMethodName');
        $method->setAccessible(true);

        return (string) $method->invoke($handler, $module);
    }

    /**
     * The code under test only reads the language out of the context, so these do not have to be
     * installed. Building them in memory keeps the test meaningful on a shop with a single language,
     * which is what the integration bootstrap provides.
     *
     * @return array{Language, Language}
     */
    private function twoDistinctLanguages(): array
    {
        $employeeLanguage = new Language();
        $employeeLanguage->id = 1;
        $employeeLanguage->iso_code = 'en';

        $orderLanguage = new Language();
        $orderLanguage->id = 2;
        $orderLanguage->iso_code = 'de';

        self::assertNotSame($employeeLanguage->iso_code, $orderLanguage->iso_code);

        return [$employeeLanguage, $orderLanguage];
    }
}

/**
 * Stands in for a real payment module: records the context language at construction time, exactly the way
 * a module's constructor resolves its displayName through trans(). The parent constructor is skipped on
 * purpose, so the fixture needs no module installed on disk.
 */
class LanguageStampedPaymentModule extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'languagestampedpaymentmodule';
        $this->displayName = 'name-in-' . Context::getContext()->language->iso_code;
    }
}

class NamelessPaymentModule extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'namelesspaymentmodule';
        $this->displayName = '';
    }
}
