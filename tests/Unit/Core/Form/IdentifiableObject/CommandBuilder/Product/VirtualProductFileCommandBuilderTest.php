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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\VirtualProductFileCommandBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Resources\DummyFileUploader;

class VirtualProductFileCommandBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new VirtualProductFileCommandBuilder();
        $builtCommands = $builder->buildCommand($this->getProductId(), $formData);
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
                'virtual_product_file' => [
                    'file' => $dummyFile,
                    'name' => 'The file',
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
                'virtual_product_file' => [
                    'file' => $dummyFile,
                    'name' => 'The file',
                    'access_days_limit' => 1,
                    'download_times_limit' => 5,
                    'expiration_date' => '2020-10-20',
                ],
            ],
            [$command],
        ];

        //@todo: add tests for update when related command is merged https://github.com/PrestaShop/PrestaShop/pull/23386
    }
}
