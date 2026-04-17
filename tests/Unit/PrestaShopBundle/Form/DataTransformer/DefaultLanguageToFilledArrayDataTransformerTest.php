<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\DataTransformer\DefaultLanguageToFilledArrayDataTransformer;

/**
 * Class SingleDefaultLanguageArrayToFilledArrayDataTransformerTest
 */
class DefaultLanguageToFilledArrayDataTransformerTest extends TestCase
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    public function setUp(): void
    {
        parent::setUp();

        $this->defaultLanguageId = 1;
    }

    /**
     * @dataProvider getInvalidValuesForModification
     */
    public function testReverseTransformationItReturnsSameValueAsPassed($item)
    {
        $dataTransformer = new DefaultLanguageToFilledArrayDataTransformer($this->defaultLanguageId);
        $result = $dataTransformer->reverseTransform($item);

        $this->assertEquals($item, $result);
    }

    public function getInvalidValuesForModification()
    {
        return [
            [
                [],
            ],
            [
                [
                    2 => 'my text',
                    3 => '',
                ],
            ],
            [
                [
                    $this->defaultLanguageId => 'test1',
                    2 => 'test2',
                    3 => 'test3',
                ],
            ],
        ];
    }

    public function testReverseTransformationItReturnsFilledArray()
    {
        $dataTransformer = new DefaultLanguageToFilledArrayDataTransformer($this->defaultLanguageId);
        $result = $dataTransformer->reverseTransform([
            $this->defaultLanguageId => 'default language text',
            2 => 'another text should be left untouched',
            3 => '',
        ]);

        $this->assertEquals(
            [
                $this->defaultLanguageId => 'default language text',
                2 => 'another text should be left untouched',
                3 => 'default language text',
            ],
            $result
        );
    }
}
