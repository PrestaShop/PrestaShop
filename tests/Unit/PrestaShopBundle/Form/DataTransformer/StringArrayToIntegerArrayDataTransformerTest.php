<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\DataTransformer\StringArrayToIntegerArrayDataTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringArrayToIntegerArrayDataTransformerTest
 */
class StringArrayToIntegerArrayDataTransformerTest extends TestCase
{
    /**
     * @var DataTransformerInterface
     */
    private $dataTransformer;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataTransformer = new StringArrayToIntegerArrayDataTransformer();
    }

    public function testReverseTransformationForNonArrayValue()
    {
        $data = null;

        $this->assertEquals($data, $this->dataTransformer->reverseTransform($data));
    }

    public function testReverseTransformationForStringArrayValue()
    {
        $data = [
            '1',
            '2',
            '3',
        ];

        $this->assertEquals(
            [
                1,
                2,
                3,
            ],
            $this->dataTransformer->reverseTransform($data)
        );
    }

    public function testReverseTransformationForMixedContentArrayValue()
    {
        $data = [
            '1',
            null,
            [],
        ];

        $this->assertEquals(
            [
                1,
                0,
                0,
            ],
            $this->dataTransformer->reverseTransform($data)
        );
    }
}
