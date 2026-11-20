<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Kpi\AverageCustomerAgeKpi;
use PrestaShop\PrestaShop\Adapter\Kpi\MainCountryKpi;
use PrestaShop\PrestaShop\Adapter\Kpi\MostCommonCustomersGenderKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Smarty;
use Smarty_Internal_Template;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocalizedKpiTest extends TestCase
{
    private Context $previousContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContext = Context::getContext();
        if (!defined('_PS_BO_ALL_THEMES_DIR_')) {
            define('_PS_BO_ALL_THEMES_DIR_', _PS_ROOT_DIR_ . '/admin-dev/themes/');
        }
    }

    protected function tearDown(): void
    {
        Context::setInstanceForTesting($this->previousContext);
        parent::tearDown();
    }

    /**
     * @dataProvider provideLocalizedConfigurations
     */
    public function testRenderSelectsEmployeeLanguage(
        string $kpiClass,
        string $key,
        mixed $values,
        mixed $expires,
        int $employeeIdLang,
        ?string $expectedValue,
        bool $expectedRefresh
    ): void {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->exactly(2))->method('get')->willReturnMap([
            [$key, $values],
            [$key . '_EXPIRE', $expires],
        ]);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $template = $this->getMockBuilder(Smarty_Internal_Template::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['assign', 'fetch'])
            ->getMock();
        $template->expects($this->once())->method('assign')->with($this->callback(
            function (array $variables) use ($expectedValue, $expectedRefresh): bool {
                self::assertSame($expectedValue, $variables['value']);
                self::assertSame($expectedRefresh, $variables['refresh']);
                self::assertSame('/refresh-kpi', $variables['source']);

                return true;
            }
        ));
        $template->expects($this->once())->method('fetch')->willReturn('rendered KPI');

        $smarty = $this->getMockBuilder(Smarty::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createTemplate', 'setTemplateDir'])
            ->getMock();
        $smarty->expects($this->once())->method('createTemplate')
            ->with('helpers/kpi/kpi.tpl', $smarty)
            ->willReturn($template);
        $template->smarty = $smarty;

        $context = new Context();
        $context->smarty = $smarty;
        Context::setInstanceForTesting($context);

        $kpi = new $kpiClass($translator, $configuration, '/refresh-kpi', $employeeIdLang);

        self::assertSame('rendered KPI', $kpi->render());
    }

    public static function provideLocalizedConfigurations(): iterable
    {
        $cases = [
            'single language, future expiry' => [[1 => 'Selected'], [1 => PHP_INT_MAX], 1, 'Selected', false],
            'single language, past expiry' => [[1 => 'Selected'], [1 => 1], 1, 'Selected', true],
            'multiple languages, select second' => [[1 => 'Other', 2 => 'Selected'], [1 => 1, 2 => PHP_INT_MAX], 2, 'Selected', false],
            'multiple languages, selected expiry is past' => [[1 => 'Other', 2 => 'Selected'], [1 => PHP_INT_MAX, 2 => 1], 2, 'Selected', true],
            'missing language' => [[1 => 'Other'], [1 => PHP_INT_MAX], 2, null, true],
            'empty cache' => [[], [], 1, null, true],
            'null cache' => [null, null, 1, null, true],
            'false cache' => [false, false, 1, null, true],
            'null localized entries' => [[1 => null], [1 => null], 1, null, true],
            'missing value, valid expiry' => [null, [1 => PHP_INT_MAX], 1, null, false],
            'missing expiry' => [[1 => 'Selected'], null, 1, 'Selected', true],
            'expiry for another language' => [[1 => 'Selected'], [2 => PHP_INT_MAX], 1, 'Selected', true],
            'empty localized value' => [[1 => ''], [1 => PHP_INT_MAX], 1, '', false],
            'zero localized value' => [[1 => '0'], [1 => PHP_INT_MAX], 1, '0', false],
        ];

        foreach ([
            MostCommonCustomersGenderKpi::class => 'CUSTOMER_MAIN_GENDER',
            AverageCustomerAgeKpi::class => 'AVG_CUSTOMER_AGE',
            MainCountryKpi::class => 'MAIN_COUNTRY',
        ] as $class => $key) {
            foreach ($cases as $name => $case) {
                yield $key . ': ' . $name => array_merge([$class, $key], $case);
            }
        }
    }
}
