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

use DateTimeImmutable;
use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\VirtualProductFileCommandsBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Resources\DummyFileUploader;

class VirtualProductFileCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new VirtualProductFileCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        $dummyFile = new UploadedFile(
            DummyFileUploader::getDummyFilesPath() . 'app_icon.png',
            'app_icon.png'
        );

        $command = new AddVirtualProductFileCommand(
            $this->getProductId()->getValue(),
            $dummyFile->getPathname(),
            'The file'
        );
        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => true,
                        'virtual_product_file_id' => '0',
                        'file' => $dummyFile,
                        'name' => 'The file',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new AddVirtualProductFileCommand(
            $this->getProductId()->getValue(),
            $dummyFile->getPathname(),
            'The file',
            1,
            5,
            new DateTimeImmutable('2020-10-20')
        );
        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => true,
                        'virtual_product_file_id' => null,
                        'file' => $dummyFile,
                        'name' => 'The file',
                        'access_days_limit' => 1,
                        'download_times_limit' => 5,
                        'expiration_date' => '2020-10-20',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateVirtualProductFileCommand(5);
        $command->setFilePath($dummyFile->getPathname());
        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => true,
                        'virtual_product_file_id' => 5,
                        'file' => $dummyFile,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateVirtualProductFileCommand(6);
        $command->setDisplayName('new display name');
        $command->setAccessDays(10);
        $command->setDownloadTimesLimit(50);
        $command->setExpirationDate(new DateTimeImmutable('2020-10-21'));
        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => true,
                        'virtual_product_file_id' => '6',
                        'name' => 'new display name',
                        'access_days_limit' => 10,
                        'download_times_limit' => '50',
                        'expiration_date' => '2020-10-21',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new DeleteVirtualProductFileCommand(17);
        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => 0,
                        'virtual_product_file_id' => 17,
                    ],
                ],
            ],
            [$command],
        ];

        yield [
            [
                'stock' => [
                    'virtual_product_file' => [
                        'has_file' => 0,
                        'virtual_product_file_id' => 0,
                    ],
                ],
            ],
            [],
        ];

        yield [
            [
                'virtual_product_file' => [
                    'has_file' => false,
                ],
            ],
            [],
        ];
    }
}
