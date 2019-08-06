<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\Product\Image\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImagesAssociationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ExistingProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ProductImage;

class UpdateProductImagesCommandTest extends TestCase
{
    /**
     * @dataProvider provideImages
     */
    public function testItBuildsImagesCorrectly(array $expected, array $images): void
    {
        $command = new UpdateProductImagesAssociationCommand(1, $images);

        $this->assertEquals($expected, $command->getImages());
    }

    public function provideImages(): ?\Generator
    {
        yield [
            [
                new ExistingProductImage(1, 1, false, []),
                new ExistingProductImage(2, 2, true, ['caption'])
            ],
            [
                [
                    'id' => 1,
                    'position' => 1,
                    'is_cover' => false,
                    'captions' => [],
                ],
                [
                    'id' => 2,
                    'position' => 2,
                    'is_cover' => true,
                    'captions' => ['caption'],
                ]
            ]
        ];

        yield [
            [
                new ProductImage(1, false, []),
                new ProductImage( 2, true, ['caption'])
            ],
            [
                [
                    'position' => 1,
                    'is_cover' => false,
                    'captions' => [],
                ],
                [
                    'position' => 2,
                    'is_cover' => true,
                    'captions' => ['caption'],
                ]
            ]
        ];

        yield [
            [
                new ProductImage(1, false, []),
                new ExistingProductImage(1, 2, true, ['caption'])
            ],
            [
                [
                    'position' => 1,
                    'is_cover' => false,
                    'captions' => [],
                ],
                [
                    'id' => 1,
                    'position' => 2,
                    'is_cover' => true,
                    'captions' => ['caption'],
                ]
            ]
        ];
    }
}
