<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Core\Domain\ImageSettings\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

class EditableImageTypeTest extends TestCase
{
    public function testConstruct(): void
    {
        $imageTypeId = new ImageTypeId(10);
        $instance = new EditableImageType(
            $imageTypeId,
            'name',
            123,
            455,
            true,
            true,
            true,
            true,
            true,
        );

        self::assertSame($imageTypeId, $instance->getImageTypeId());
        self::assertSame('name', $instance->getName());
        self::assertSame(123, $instance->getWidth());
        self::assertSame(455, $instance->getHeight());
        self::assertTrue($instance->isProducts());
        self::assertTrue($instance->isCategories());
        self::assertTrue($instance->isManufacturers());
        self::assertTrue($instance->isSuppliers());
        self::assertTrue($instance->isStores());
    }
}
