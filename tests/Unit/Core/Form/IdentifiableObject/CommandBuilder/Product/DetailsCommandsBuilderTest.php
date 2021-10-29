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

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductDetailsCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\DetailsCommandsBuilder;

class DetailsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new DetailsCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'specifications' => [
                    'references' => [
                    ],
                ],
            ],
            [],
        ];

        $command = new UpdateProductDetailsCommand($this->getProductId()->getValue());
        $command->setReference('ref');
        yield [
            [
                'specifications' => [
                    'references' => [
                        'reference' => 'ref',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductDetailsCommand($this->getProductId()->getValue());
        $command->setIsbn('1234');
        yield [
            [
                'specifications' => [
                    'references' => [
                        'isbn' => '1234',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductDetailsCommand($this->getProductId()->getValue());
        $command->setEan13('13');
        yield [
            [
                'specifications' => [
                    'references' => [
                        'ean_13' => '13',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductDetailsCommand($this->getProductId()->getValue());
        $command->setUpc('1345');
        yield [
            [
                'specifications' => [
                    'references' => [
                        'upc' => '1345',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductDetailsCommand($this->getProductId()->getValue());
        $command->setMpn('mpn');
        yield [
            [
                'specifications' => [
                    'references' => [
                        'mpn' => 'mpn',
                    ],
                ],
            ],
            [$command],
        ];
    }
}
