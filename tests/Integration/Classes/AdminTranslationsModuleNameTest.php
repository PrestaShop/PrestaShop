<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminTranslationsController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The modules translation screen only loads the translation files of installed modules:
 * a requested module that is not in the installed modules list is ignored.
 */
class AdminTranslationsModuleNameTest extends TestCase
{
    private const LOADED = 'ps_translations_module_name_test_loaded';

    /**
     * @var string
     */
    private $workDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDir = sys_get_temp_dir() . '/ps-translations-' . uniqid();
        $GLOBALS[self::LOADED] = [];

        $this->writeTranslationFile($this->workDir . '/modules/installedmodule/translations/en.php', 'installedmodule');
        $this->writeTranslationFile($this->workDir . '/elsewhere/othermodule/translations/en.php', 'othermodule');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS[self::LOADED]);
        (new Filesystem())->remove($this->workDir);

        parent::tearDown();
    }

    public function testInstalledModuleTranslationFileIsLoaded(): void
    {
        $this->getAllModuleFiles(['installedmodule']);

        self::assertSame(['installedmodule'], $GLOBALS[self::LOADED]);
    }

    public function testModuleOutsideTheInstalledListIsIgnored(): void
    {
        $this->getAllModuleFiles([$this->workDir . '/elsewhere/othermodule']);

        self::assertSame([], $GLOBALS[self::LOADED]);
    }

    private function getAllModuleFiles(array $modules): void
    {
        $controller = $this->getMockBuilder(AdminTranslationsController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getListModules', 'getModuleTranslations', 'recursiveGetModuleFiles'])
            ->getMock();
        $controller->method('getListModules')->willReturn(['installedmodule']);

        $this->setProperty($controller, 'type_selected', 'modules');
        $this->setProperty($controller, 'theme_selected', false);
        $this->setProperty($controller, 'translations_informations', [
            'modules' => ['dir' => $this->workDir . '/modules/'],
        ]);

        $method = new ReflectionMethod(AdminTranslationsController::class, 'getAllModuleFiles');
        $method->setAccessible(true);
        $method->invoke($controller, $modules, null, 'en', true);
    }

    private function writeTranslationFile(string $path, string $name): void
    {
        (new Filesystem())->dumpFile(
            $path,
            sprintf("<?php\n\$GLOBALS['%s'][] = '%s';\n", self::LOADED, $name)
        );
    }

    private function setProperty(object $object, string $name, $value): void
    {
        $property = (new ReflectionClass(AdminTranslationsController::class))->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
