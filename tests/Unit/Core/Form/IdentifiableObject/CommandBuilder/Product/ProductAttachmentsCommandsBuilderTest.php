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
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\SetAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductAttachmentsCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilderInterface;

class ProductAttachmentsCommandsBuilderTest extends AbstractProductCommandBuilderTest
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
