<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Translate;

class TranslateTest extends TestCase
{
    use ContextMockerTrait;

    private const LOCALE = 'en-US';
    private const ISO = 'en';
    private const WORDING = 'Hello';

    /**
     * @var string[] module directories created by a test
     */
    private array $createdModuleDirs = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdModuleDirs as $dir) {
            foreach (['/translations/' . self::ISO . '.php', '/' . self::ISO . '.php'] as $file) {
                if (file_exists($dir . $file)) {
                    unlink($dir . $file);
                }
            }
            if (is_dir($dir . '/translations')) {
                rmdir($dir . '/translations');
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
        $this->createdModuleDirs = [];

        parent::tearDown();
    }

    /**
     * The back office translation pages only read and write modules/<module>/translations/<iso>.php, so
     * when a module also ships the 1.4 style modules/<module>/<iso>.php the front office has to prefer the
     * former, otherwise nothing translated from the back office is ever displayed.
     */
    public function testItPrefersTheTranslationsDirectoryOverTheModuleRoot(): void
    {
        // A distinct module name per case is required: getModuleTranslation() keeps a static per module
        // and iso cache of the merged files, so reusing a name would assert against the first merge.
        $module = $this->createModule('qatranslatepriority', 'from-root', 'from-translations-dir');

        self::assertSame(
            'from-translations-dir',
            Translate::getModuleTranslation($module, self::WORDING, $module, null, false, self::LOCALE)
        );
    }

    public function testItStillUsesTheModuleRootWhenItIsTheOnlyFile(): void
    {
        $module = $this->createModule('qatranslaterootonly', 'from-root', null);

        self::assertSame(
            'from-root',
            Translate::getModuleTranslation($module, self::WORDING, $module, null, false, self::LOCALE)
        );
    }

    public function testItUsesTheTranslationsDirectoryWhenItIsTheOnlyFile(): void
    {
        $module = $this->createModule('qatranslatedironly', null, 'from-translations-dir');

        self::assertSame(
            'from-translations-dir',
            Translate::getModuleTranslation($module, self::WORDING, $module, null, false, self::LOCALE)
        );
    }

    /**
     * @param string $module module name, must be unique per test
     * @param string|null $rootValue wording stored in modules/<module>/<iso>.php, null to skip the file
     * @param string|null $translationsValue wording stored in modules/<module>/translations/<iso>.php
     *
     * @return string the module name
     */
    private function createModule(string $module, ?string $rootValue, ?string $translationsValue): string
    {
        $dir = _PS_MODULE_DIR_ . $module;
        @mkdir($dir . '/translations', 0777, true);
        $this->createdModuleDirs[] = $dir;

        $key = strtolower('<{' . $module . '}prestashop>' . $module) . '_' . md5(self::WORDING);

        if (null !== $rootValue) {
            file_put_contents($dir . '/' . self::ISO . '.php', $this->buildFile($key, $rootValue));
        }
        if (null !== $translationsValue) {
            file_put_contents($dir . '/translations/' . self::ISO . '.php', $this->buildFile($key, $translationsValue));
        }

        return $module;
    }

    private function buildFile(string $key, string $value): string
    {
        return sprintf("<?php\nglobal \$_MODULE;\n\$_MODULE = [];\n\$_MODULE['%s'] = '%s';\n", $key, $value);
    }
}
