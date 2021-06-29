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
        $dataTransformer = new DefaultLanguageToFilledArrayDataTransformer((string) $this->defaultLanguageId);
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
