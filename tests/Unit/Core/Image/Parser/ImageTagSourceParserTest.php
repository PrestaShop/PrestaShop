<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Image\Parser;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParser;

class ImageTagSourceParserTest extends TestCase
{
    /**
     * @var ImageTagSourceParser
     */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new ImageTagSourceParser();
    }

    /**
     * @dataProvider getTestCases
     */
    public function testItParsesSourceAsExpected($imageTag, $expectedSource)
    {
        $parsedSource = $this->parser->parse($imageTag);

        $this->assertSame($expectedSource, $parsedSource);
    }

    public function getTestCases()
    {
        return [
            [
                '<img src="/path/to/my_image.jpg">',
                '/path/to/my_image.jpg',
            ],
            [
                '<img src="../path/to/my_image.jpg">',
                '/path/to/my_image.jpg',
            ],
            [
                '<img class="test" src="../path/to/my_image.jpg" alt="some alt text">',
                '/path/to/my_image.jpg',
            ],
            [
                '<img class="test" src="../path/to/my_image.jpg?time=123" alt="some alt text">',
                '/path/to/my_image.jpg?time=123',
            ],
            [
                '<img class="test" src="../.././path/to/my_image.jpg?time=123" alt="some alt text">',
                '/path/to/my_image.jpg?time=123',
            ],
            [
                '<img class="test" src="../../../../../../../path/to/my_image.jpg?time=123" alt="some alt text">',
                '/path/to/my_image.jpg?time=123',
            ],
            [
                '<img class="test" src="./../../../path/to/my_image.jpg?time=123" alt="some alt text">',
                '/path/to/my_image.jpg?time=123',
            ],
            [
                '<img class="test">',
                null,
            ],
            [
                'random string',
                null,
            ],
            [
                '<img class="">',
                null,
            ],
        ];
    }
}
