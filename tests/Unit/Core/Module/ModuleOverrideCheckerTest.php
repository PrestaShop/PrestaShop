<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\ModuleOverrideChecker;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleOverrideCheckerTest extends TestCase
{
    /**
     * @var string
     */
    protected $psOverrideDir;

    /**
     * @var string
     */
    protected $modulesTestsDir;

    protected function setUp(): void
    {
        $this->psOverrideDir = dirname(__DIR__, 3) . '/Resources/modules_tests/override_for_unit_test/';
        $this->modulesTestsDir = dirname(__DIR__, 3) . '/Resources/modules_tests';
    }

    /**
     * @dataProvider provideTestData
     */
    public function testHasOverrideConflict(string $moduleName, bool $expectedResult): void
    {
        $moduleOverrideChecker = $this->getModuleOverrideChecker();

        $moduleOverridePath = sprintf('%s/%s/override', $this->modulesTestsDir, $moduleName);

        $this->assertEquals($expectedResult, $moduleOverrideChecker->hasOverrideConflict($moduleOverridePath));
    }

    private function getModuleOverrideChecker(): ModuleOverrideChecker
    {
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturnArgument(0);

        return new ModuleOverrideChecker($translatorMock, $this->psOverrideDir);
    }

    public function provideTestData(): array
    {
        return [
            [
                'testnoconflict',
                false,
            ],
            [
                'testnooverride',
                false,
            ],
            [
                'testbasicconflict',
                true,
            ],
            [
                'testtrickyconflict',
                true,
            ],
            [
                'testpropertyconflict',
                true,
            ],
            [
                'testconstantconflict',
                true,
            ],
        ];
    }
}
