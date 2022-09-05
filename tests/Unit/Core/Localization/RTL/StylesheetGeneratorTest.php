<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
