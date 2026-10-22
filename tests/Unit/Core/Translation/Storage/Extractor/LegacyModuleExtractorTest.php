<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Extractor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\PhpExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\TwigExtractor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;

class LegacyModuleExtractorTest extends TestCase
{
    private string $modulesDir;

    private string $overrideModulesDir;

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $base = sys_get_temp_dir() . '/ps_legacy_module_extractor_' . uniqid('', true);
        $this->modulesDir = $base . '/modules';
        $this->overrideModulesDir = $base . '/override/modules';
        $this->filesystem->mkdir($this->modulesDir . '/dummymodule');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(dirname($this->modulesDir));
    }

    public function testItAlsoExtractsFromOverrideModulesDirectoryWhenPresent(): void
    {
        $this->filesystem->mkdir($this->overrideModulesDir . '/dummymodule');

        $modulePath = $this->modulesDir . '/dummymodule';
        $overridePath = $this->overrideModulesDir . '/dummymodule';

        /** @var PhpExtractor&MockObject $phpExtractor */
        $phpExtractor = $this->createMock(PhpExtractor::class);
        $phpExtractor->method('setExcludedDirectories')->willReturnSelf();
        $phpExtractor->expects($this->exactly(2))
            ->method('extract')
            ->with(
                $this->callback(function (string $path) use ($modulePath, $overridePath): bool {
                    static $call = 0;
                    ++$call;

                    return 1 === $call ? $path === $modulePath : $path === $overridePath;
                }),
                $this->isInstanceOf(MessageCatalogue::class)
            );

        /** @var SmartyExtractor&MockObject $smartyExtractor */
        $smartyExtractor = $this->createMock(SmartyExtractor::class);
        $smartyExtractor->method('setExcludedDirectories')->willReturnSelf();
        $smartyExtractor->expects($this->exactly(2))
            ->method('extract')
            ->with(
                $this->callback(function (string $path) use ($modulePath, $overridePath): bool {
                    static $call = 0;
                    ++$call;

                    return 1 === $call ? $path === $modulePath : $path === $overridePath;
                }),
                $this->isInstanceOf(MessageCatalogue::class)
            );

        /** @var TwigExtractor&MockObject $twigExtractor */
        $twigExtractor = $this->createMock(TwigExtractor::class);
        $twigExtractor->method('setExcludedDirectories')->willReturnSelf();
        $twigExtractor->expects($this->exactly(2))
            ->method('extract')
            ->with(
                $this->callback(function (string $path) use ($modulePath, $overridePath): bool {
                    static $call = 0;
                    ++$call;

                    return 1 === $call ? $path === $modulePath : $path === $overridePath;
                }),
                $this->isInstanceOf(MessageCatalogue::class)
            );

        $extractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->modulesDir,
            ['vendor'],
            $this->overrideModulesDir
        );

        $catalogue = $extractor->extract('dummymodule', 'en-US');

        $this->assertSame('en-US', $catalogue->getLocale());
    }

    public function testItSkipsOverrideExtractionWhenDirectoryIsMissing(): void
    {
        $modulePath = $this->modulesDir . '/dummymodule';

        /** @var PhpExtractor&MockObject $phpExtractor */
        $phpExtractor = $this->createMock(PhpExtractor::class);
        $phpExtractor->method('setExcludedDirectories')->willReturnSelf();
        $phpExtractor->expects($this->once())
            ->method('extract')
            ->with($modulePath, $this->isInstanceOf(MessageCatalogue::class));

        /** @var SmartyExtractor&MockObject $smartyExtractor */
        $smartyExtractor = $this->createMock(SmartyExtractor::class);
        $smartyExtractor->method('setExcludedDirectories')->willReturnSelf();
        $smartyExtractor->expects($this->once())
            ->method('extract')
            ->with($modulePath, $this->isInstanceOf(MessageCatalogue::class));

        /** @var TwigExtractor&MockObject $twigExtractor */
        $twigExtractor = $this->createMock(TwigExtractor::class);
        $twigExtractor->method('setExcludedDirectories')->willReturnSelf();
        $twigExtractor->expects($this->once())
            ->method('extract')
            ->with($modulePath, $this->isInstanceOf(MessageCatalogue::class));

        $extractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->modulesDir,
            ['vendor'],
            $this->overrideModulesDir
        );

        $extractor->extract('dummymodule', 'en-US');
    }
}
