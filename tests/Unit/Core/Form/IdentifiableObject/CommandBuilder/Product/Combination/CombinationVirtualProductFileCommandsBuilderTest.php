<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use DateTimeImmutable;
use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationVirtualProductFileCommandsBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Resources\DummyFileUploader;

class CombinationVirtualProductFileCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    private const PRODUCT_ID = 51;

    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationVirtualProductFileCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommands(): Generator
    {
        yield 'no virtual_product_file key at all' => [
            [
                'product_id' => self::PRODUCT_ID,
                'no data' => ['useless value'],
            ],
            [],
        ];

        $dummyFile = new UploadedFile(
            DummyFileUploader::getDummyFilesPath() . 'app_icon.png',
            'app_icon.png'
        );

        // Add command must carry the combination id
        $command = new AddVirtualProductFileCommand(
            self::PRODUCT_ID,
            $dummyFile->getPathname(),
            'The file'
        );
        $command->setCombinationId($this->getCombinationId()->getValue());
        yield 'add minimal file' => [
            [
                'product_id' => self::PRODUCT_ID,
                'virtual_product_file' => [
                    'has_file' => true,
                    'virtual_product_file_id' => '0',
                    'file' => $dummyFile,
                    'name' => 'The file',
                ],
            ],
            [$command],
        ];

        $command = new AddVirtualProductFileCommand(
            self::PRODUCT_ID,
            $dummyFile->getPathname(),
            'The file',
            1,
            5,
            new DateTimeImmutable('2020-10-20')
        );
        $command->setCombinationId($this->getCombinationId()->getValue());
        yield 'add full file' => [
            [
                'product_id' => self::PRODUCT_ID,
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
            [$command],
        ];

        $command = new UpdateVirtualProductFileCommand(6);
        $command->setDisplayName('new display name');
        $command->setAccessDays(10);
        $command->setDownloadTimesLimit(50);
        $command->setExpirationDate(new DateTimeImmutable('2020-10-21'));
        yield 'update existing file' => [
            [
                'product_id' => self::PRODUCT_ID,
                'virtual_product_file' => [
                    'has_file' => true,
                    'virtual_product_file_id' => '6',
                    'name' => 'new display name',
                    'access_days_limit' => 10,
                    'download_times_limit' => '50',
                    'expiration_date' => '2020-10-21',
                ],
            ],
            [$command],
        ];

        $command = new DeleteVirtualProductFileCommand(17);
        yield 'delete existing file' => [
            [
                'product_id' => self::PRODUCT_ID,
                'virtual_product_file' => [
                    'has_file' => 0,
                    'virtual_product_file_id' => 17,
                ],
            ],
            [$command],
        ];

        yield 'no file and nothing to delete' => [
            [
                'product_id' => self::PRODUCT_ID,
                'virtual_product_file' => [
                    'has_file' => 0,
                    'virtual_product_file_id' => 0,
                ],
            ],
            [],
        ];

        yield 'switch off without a file id' => [
            [
                'product_id' => self::PRODUCT_ID,
                'virtual_product_file' => [
                    'has_file' => false,
                ],
            ],
            [],
        ];
    }
}
