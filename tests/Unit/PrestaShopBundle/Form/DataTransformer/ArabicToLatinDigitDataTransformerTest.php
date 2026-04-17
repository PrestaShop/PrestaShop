<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;
use PrestaShopBundle\Form\DataTransformer\ArabicToLatinDigitDataTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ArabicToLatinDigitDataTransformerTest
 */
class ArabicToLatinDigitDataTransformerTest extends TestCase
{
    /**
     * @var DataTransformerInterface
     */
    private $dataTransformer;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataTransformer = new ArabicToLatinDigitDataTransformer(new ArabicToLatinDigitConverter());
    }

    public function testReverseTransformationForNullValue()
    {
        $data = null;

        $this->assertEquals($data, $this->dataTransformer->reverseTransform($data));
    }

    public function testReverseTransformationForLatinDigits()
    {
        $data = '0123456789';

        $this->assertEquals('0123456789', $this->dataTransformer->reverseTransform($data));
    }

    public function testReverseTransformationForArabicDigits()
    {
        $data = '٠١٢٣٤٥٦٧٨٩';

        $this->assertEquals('0123456789', $this->dataTransformer->reverseTransform($data));
    }

    public function testReverseTransformationForPersianDigits()
    {
        $data = '۰۱۲۳۴۵۶۷۸۹';

        $this->assertEquals('0123456789', $this->dataTransformer->reverseTransform($data));
    }

    // transform() method should not actually transform the data
    public function testTransformationForNullValue()
    {
        $data = null;

        $this->assertEquals($data, $this->dataTransformer->transform($data));
    }

    public function testTransformationForLatinDigits()
    {
        $data = '0123456789';

        $this->assertEquals('0123456789', $this->dataTransformer->transform($data));
    }

    public function testTransformationForArabicDigits()
    {
        $data = '٠١٢٣٤٥٦٧٨٩';

        $this->assertEquals('٠١٢٣٤٥٦٧٨٩', $this->dataTransformer->transform($data));
    }

    public function testTransformationForPersianDigits()
    {
        $data = '۰۱۲۳۴۵۶۷۸۹';

        $this->assertEquals('۰۱۲۳۴۵۶۷۸۹', $this->dataTransformer->transform($data));
    }
}
