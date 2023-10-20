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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
