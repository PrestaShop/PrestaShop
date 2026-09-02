<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\RTL;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\RTL\StylesheetGenerator;
use Symfony\Component\Filesystem\Filesystem;

class StylesheetGeneratorTest extends TestCase
{
    private const RTL_INPUT_FILENAME = 'rtl_input.css';
    private const RTL_INPUT_FILENAME_WITH_SUFFIX = 'rtl_input_rtl.css';
    private const RTL_OUTPUT_FILENAME = 'rtl_output.css';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /** @var string */
    protected $cssSamplesDirectory;
    /** @var string */
    protected $sandboxDirectory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->sandboxDirectory = sys_get_temp_dir() . '/StylesheetGeneratorTest';
        $this->cssSamplesDirectory = dirname(__DIR__, 3) . '/Resources/assets/css';

        $this->filesystem->mkdir($this->sandboxDirectory);
        $this->filesystem->copy(
            $this->cssSamplesDirectory . '/' . self::RTL_INPUT_FILENAME,
            $this->sandboxDirectory . '/' . self::RTL_INPUT_FILENAME
        );
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->sandboxDirectory . '/' . self::RTL_INPUT_FILENAME);
        $this->filesystem->remove($this->sandboxDirectory . '/' . self::RTL_INPUT_FILENAME_WITH_SUFFIX);
    }

    public function testGeneration(): void
    {
        $generator = new StylesheetGenerator();
        $generator->generateInDirectory($this->sandboxDirectory);

        $expectedCssFileContent = file_get_contents($this->cssSamplesDirectory . '/' . self::RTL_OUTPUT_FILENAME);
        $generatedCssFileContent = file_get_contents($this->sandboxDirectory . '/' . self::RTL_INPUT_FILENAME_WITH_SUFFIX);

        $this->assertEquals($expectedCssFileContent, $generatedCssFileContent);
    }
}
