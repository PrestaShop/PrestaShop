<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\SetAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductAttachmentsCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilderInterface;

class ProductAttachmentsCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
{
    /**
     * @var ProductCommandsBuilderInterface
     */
    private $commandsBuilder;

    protected function setUp(): void
    {
        $this->commandsBuilder = new ProductAttachmentsCommandsBuilder();
    }

    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builtCommands = $this->commandsBuilder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'how did I get here?' => ['useless val'],
            ],
            [],
        ];

        yield [
            [
                'details' => null,
            ],
            [],
        ];

        yield [
            [
                'details' => [
                    'attachments' => null,
                ],
            ],
            [],
        ];

        yield [
            [
                'details' => [
                    'attachments' => [],
                ],
            ],
            [],
        ];

        yield [
            [
                'details' => [
                    'attachments' => [
                        'attached_files' => [],
                    ],
                ],
            ],
            [new RemoveAllAssociatedProductAttachmentsCommand($this->getProductId()->getValue())],
        ];

        yield [
            [
                'details' => [
                    'attachments' => [
                        'attached_files' => [
                            [
                                'attachment_id' => '1',
                                'name' => 'test1',
                                'filename' => 'filenametest1',
                                'mime_type' => 'image/jpeg',
                            ],
                            [
                                'attachment_id' => '2',
                                'name' => 'test2',
                                'filename' => 'filenametest2',
                                'mime_type' => 'image/png',
                            ],
                            [
                                'attachment_id' => 3,
                                'name' => 'test1',
                                'filename' => 'filenametest1',
                                'mime_type' => 'image/jpeg',
                            ],
                        ],
                    ],
                ],
            ],
            [new SetAssociatedProductAttachmentsCommand($this->getProductId()->getValue(), [1, 2, 3])],
        ];

        // Filter duplicate IDs
        yield [
            [
                'details' => [
                    'attachments' => [
                        'attached_files' => [
                            [
                                'attachment_id' => '1',
                                'name' => 'test1',
                                'filename' => 'filenametest1',
                                'mime_type' => 'image/jpeg',
                            ],
                            [
                                'attachment_id' => '2',
                                'name' => 'test2',
                                'filename' => 'filenametest2',
                                'mime_type' => 'image/png',
                            ],
                            [
                                'attachment_id' => '1',
                                'name' => 'test1',
                                'filename' => 'filenametest1',
                                'mime_type' => 'image/jpeg',
                            ],
                            [
                                'attachment_id' => 3,
                                'name' => 'test1',
                                'filename' => 'filenametest1',
                                'mime_type' => 'image/jpeg',
                            ],
                        ],
                    ],
                ],
            ],
            [new SetAssociatedProductAttachmentsCommand($this->getProductId()->getValue(), [1, 2, 3])],
        ];
    }
}
