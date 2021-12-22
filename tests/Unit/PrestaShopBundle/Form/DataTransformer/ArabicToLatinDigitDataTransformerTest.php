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
