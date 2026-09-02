<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Trait;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Trait\DirtyTrait;

class DirtyTraitMock
{
    use DirtyTrait;
}

class DirtyTraitTest extends TestCase
{
    public function testIsDirtyInitiallyEmpty(): void
    {
        $object = new DirtyTraitMock();
        $this->assertEmpty($object->getDirtyProperties());
    }

    public function testMarkDirty(): void
    {
        $object = new DirtyTraitMock();
        $object->markDirty('someProperty');
        $object->markDirty('someProperty2');

        $this->assertCount(2, $object->getDirtyProperties());

        $this->assertContains('someProperty', $object->getDirtyProperties());
        $this->assertContains('someProperty2', $object->getDirtyProperties());
        $this->assertNotContains('someProperty3', $object->getDirtyProperties());
    }

    public function testIsDirty(): void
    {
        $object = new DirtyTraitMock();
        $object->markDirty('someProperty');
        $object->markDirty('someProperty2');

        $this->assertTrue($object->isDirty('someProperty'));
        $this->assertTrue($object->isDirty('someProperty2'));
        $this->assertFalse($object->isDirty('someProperty3'));
    }
}
