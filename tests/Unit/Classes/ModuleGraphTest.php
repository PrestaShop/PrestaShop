<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use Error;
use ModuleGraph;
use PHPUnit\Framework\TestCase;

class ModuleGraphTest extends TestCase
{
    public static $data;

    /**
     * @var ModuleGraph
     */
    private $object;

    protected function setUp(): void
    {
        $this->object = new class() extends ModuleGraph {
            public function getData($layers)
            {
                return ModuleGraphTest::$data;
            }
        };
    }

    public function testDrawWithoutInitialization()
    {
        $this->expectException(Error::class);
        $this->object->draw();
    }

    /**
     * @dataProvider getEscapeCellValues
     */
    public function testEscapeCell($base, $expected)
    {
        $this->assertEquals(
            $this->object->escapeCell($base),
            $expected
        );
    }

    public function getEscapeCellValues()
    {
        return [
            ['=CMD', '"\'=CMD"'],
            ['@here', '"\'@here"'],
            ['+something', '"\'+something"'],
            ['-thing', '"\'-thing"'],
            ['hello world', '"hello world"'],
            ['=@+-', '"\'=@+-"'],
            ['Hey "you"!', '"Hey ""you""!"'],
        ];
    }
}
