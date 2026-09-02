<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Core\Domain\ImageSettings\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;

class EditableImageSettingsTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new EditableImageSettings(
            'jpg,avif',
            'jpg',
            90,
            90,
            8,
            90,
            2,
            499,
            123,
            456,
        );

        $this->assertSame(['jpg', 'avif'], $instance->getFormats());
        $this->assertSame('jpg', $instance->getBaseFormat());
        $this->assertSame(90, $instance->getAvifQuality());
        $this->assertSame(90, $instance->getJpegQuality());
        $this->assertSame(8, $instance->getPngQuality());
        $this->assertSame(90, $instance->getWebpQuality());
        $this->assertSame(2, $instance->getGenerationMethod());
        $this->assertSame(499, $instance->getPictureMaxSize());
        $this->assertSame(123, $instance->getPictureMaxWidth());
        $this->assertSame(456, $instance->getPictureMaxHeight());
    }
}
