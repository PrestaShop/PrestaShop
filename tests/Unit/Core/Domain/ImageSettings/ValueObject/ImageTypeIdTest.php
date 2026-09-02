<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ImageSettings\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

class ImageTypeIdTest extends TestCase
{
    /**
     * @dataProvider getValidInput
     */
    public function testValidInput(int $imageTypeId): void
    {
        $vo = new ImageTypeId($imageTypeId);
        $this->assertEquals($imageTypeId, $vo->getValue());
    }

    public function getValidInput(): iterable
    {
        yield [1000];
        yield [1];
    }

    /**
     * @dataProvider getInvalidInput
     */
    public function testInvalidInput($imageTypeId): void
    {
        $this->expectException(ImageTypeException::class);
        new ImageTypeId($imageTypeId);
    }

    public function getInvalidInput(): iterable
    {
        yield [0];
        yield [-1];
    }
}
