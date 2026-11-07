<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use AdminCountriesController;
use Configuration;
use Context;
use Country;
use Employee;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The page header shows the edited object's name, taken from a multilang field keyed by the languages
 * that actually have a row. The employee's language is not guaranteed to be one of them.
 */
class AdminToolbarTitleTest extends KernelTestCase
{
    private ?int $originalEmployeeLang = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
        Context::getContext()->employee = new Employee(1);
        $this->originalEmployeeLang = (int) Context::getContext()->employee->id_lang;
    }

    protected function tearDown(): void
    {
        if (null !== $this->originalEmployeeLang && null !== Context::getContext()->employee) {
            Context::getContext()->employee->id_lang = $this->originalEmployeeLang;
        }

        parent::tearDown();
    }

    public function testItUsesTheEmployeeLanguage(): void
    {
        $country = new Country(1);
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        Context::getContext()->employee->id_lang = $defaultLang;

        self::assertIsArray($country->name, 'the fixture must be a multilang field');
        self::assertSame($country->name[$defaultLang], $this->buildTitle($country));
    }

    public function testItFallsBackWhenTheEmployeeLanguageHasNoRow(): void
    {
        // A shop reaches this whenever a language is installed without the rows for an entity, or an
        // employee keeps a language that was later removed.
        $country = new Country(1);
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        Context::getContext()->employee->id_lang = max(array_keys($country->name)) + 100;

        // must not raise, and must show something rather than nothing
        self::assertSame($country->name[$defaultLang], $this->buildTitle($country));
    }

    public function testItPassesAScalarNameThrough(): void
    {
        $object = new Country(1);
        $object->name = 'Plain name';

        self::assertSame('Plain name', $this->buildTitle($object));
    }

    /**
     * The reported symptom: opening the country edit page as an employee whose language has no row
     * answered HTTP 500 with "htmlspecialchars(): Argument #1 ($string) must be of type string, array
     * given" from the page header builder.
     */
    public function testTheEditHeaderDoesNotFailOnAMissingLanguageRow(): void
    {
        $country = new Country(1);
        Context::getContext()->employee->id_lang = max(array_keys($country->name)) + 100;

        $controller = new class() extends AdminCountriesController {
            /** @var Country */
            public $forcedObject;

            public function loadObject($opt = false)
            {
                return $this->forcedObject;
            }

            public function forceEditDisplay(): void
            {
                $this->display = 'edit';
            }
        };
        $controller->forcedObject = $country;
        $controller->forceEditDisplay();

        $controller->initPageHeaderToolbar();

        self::assertNotEmpty($controller->page_header_toolbar_title);
        self::assertStringContainsString(
            $country->name[(int) Configuration::get('PS_LANG_DEFAULT')],
            $controller->page_header_toolbar_title
        );
    }

    private function buildTitle(Country $country): string
    {
        $controller = new class() extends AdminCountriesController {
            /** @var Country */
            public $forcedObject;

            public function loadObject($opt = false)
            {
                return $this->forcedObject;
            }

            public function readNameForToolbar(): string
            {
                return $this->getObjectNameForToolbar($this->forcedObject);
            }
        };
        $controller->forcedObject = $country;

        return $controller->readNameForToolbar();
    }
}
