<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\TagsCommandsBuilder;

class TagsCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
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
