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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\TagsCommandsBuilder;

class TagsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new TagsCommandsBuilder();
        $builtCommands = $builder->buildCommands(
            $this->getProductId(),
            $formData,
            $this->getSingleShopConstraint()
        );
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield 'empty data' => [
            [
                'no_price_data' => ['useless value'],
            ],
            [],
        ];

        yield 'empty seo data' => [
            [
                'seo' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $localizedTagsData = [
            1 => 'coton,bonbon',
            2 => 'cotton,candy',
        ];
        $localizedTags = [
            1 => ['coton', 'bonbon'],
            2 => ['cotton', 'candy'],
        ];
        $tagCommands = new SetProductTagsCommand($this->getProductId()->getValue(), $localizedTags);
        yield 'tags command' => [
            [
                'seo' => [
                    'tags' => $localizedTagsData,
                ],
            ],
            [$tagCommands],
        ];

        yield 'seo command and tags command' => [
            [
                'seo' => [
                    // redirect option should be ignored by this builder
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_CATEGORY_TEMPORARY,
                        'target' => [
                            'id' => 51,
                        ],
                    ],
                    'tags' => $localizedTagsData,
                ],
            ],
            [$tagCommands],
        ];

        $localizedTagsData = [
            1 => 'coton,bonbon',
            2 => null,
        ];
        $localizedTags = [
            1 => ['coton', 'bonbon'],
            2 => [],
        ];
        $tagCommands = new SetProductTagsCommand($this->getProductId()->getValue(), $localizedTags);
        yield 'tags with empty value for one language' => [
            [
                'seo' => [
                    'tags' => $localizedTagsData,
                ],
            ],
            [$tagCommands],
        ];

        $localizedTagsData = [
            1 => null,
            2 => null,
        ];
        $tagCommands = new RemoveAllProductTagsCommand($this->getProductId()->getValue());
        yield 'remove tags command with all localized values empty' => [
            [
                'seo' => [
                    'tags' => $localizedTagsData,
                ],
            ],
            [$tagCommands],
        ];

        $tagCommands = new RemoveAllProductTagsCommand($this->getProductId()->getValue());
        yield 'remove tags command with empty array' => [
            [
                'seo' => [
                    'tags' => [],
                ],
            ],
            [$tagCommands],
        ];

        $tagCommands = new RemoveAllProductTagsCommand($this->getProductId()->getValue());
        yield 'remove tags commands with empty string' => [
            [
                'seo' => [
                    'tags' => '',
                ],
            ],
            [$tagCommands],
        ];
    }

    public function testInvalidTags(): void
    {
        $builder = new TagsCommandsBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected tags to be a localized array');

        $builder->buildCommands(
            $this->getProductId(),
            [
                'seo' => [
                    'tags' => 'cotton, candy',
                ],
            ],
            $this->getSingleShopConstraint()
        );
    }
}
