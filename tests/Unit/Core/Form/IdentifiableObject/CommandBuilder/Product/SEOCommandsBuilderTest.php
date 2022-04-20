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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\SEOCommandsBuilder;

class SEOCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommandsForSingleShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandsForSingleShop(array $formData, array $expectedCommands)
    {
        $builder = new SEOCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands(
            $this->getProductId(),
            $formData,
            $this->getSingleShopConstraint()
        );
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsForSingleShop(): iterable
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

        $localizedMetaTitles = [
            1 => 'Titre français recherche',
            2 => 'English title seo',
        ];
        $command = $this
            ->getSingleShopCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
        ;
        yield 'meta title' => [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                ],
            ],
            [$command],
        ];

        $localizedMetaDescriptions = [
            1 => 'Description française recherche',
            2 => 'English description seo',
        ];
        $command = $this
            ->getSingleShopCommand()
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
        ;
        yield 'meta description' => [
            [
                'seo' => [
                    'meta_description' => $localizedMetaDescriptions,
                ],
            ],
            [$command],
        ];

        $localizedLinkRewrites = [
            1 => 'produit-francais',
            2 => 'english-product',
        ];
        $command = $this
            ->getSingleShopCommand()
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
        ;
        yield 'link rewrite' => [
            [
                'seo' => [
                    'link_rewrite' => $localizedLinkRewrites,
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setRedirectOption(RedirectType::TYPE_NOT_FOUND, 0)
        ;
        yield 'redirect not found' => [
            [
                'seo' => [
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_NOT_FOUND,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42)
        ;
        yield 'redirect to product' => [
            [
                'seo' => [
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_PRODUCT_TEMPORARY,
                        'target' => [
                            'id' => 42,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setRedirectOption(RedirectType::TYPE_CATEGORY_TEMPORARY, 51)
        ;
        yield 'redirect to category' => [
            [
                'seo' => [
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_CATEGORY_TEMPORARY,
                        'target' => [
                            'id' => 51,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42)
        ;
        yield 'all fields' => [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                    'meta_description' => $localizedMetaDescriptions,
                    'link_rewrite' => $localizedLinkRewrites,
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_PRODUCT_TEMPORARY,
                        'target' => [
                            'id' => 42,
                        ],
                    ],
                ],
            ],
            [$command],
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

        $command = $this
            ->getSingleShopCommand()
            ->setRedirectOption(RedirectType::TYPE_CATEGORY_TEMPORARY, 51)
        ;
        yield 'seo command and tags command' => [
            [
                'seo' => [
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_CATEGORY_TEMPORARY,
                        'target' => [
                            'id' => 51,
                        ],
                    ],
                    'tags' => $localizedTagsData,
                ],
            ],
            [$command, $tagCommands],
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
        $builder = new SEOCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected tags to be a localized array');

        $builder->buildCommands($this->getProductId(), [
            'seo' => [
                'tags' => 'cotton, candy',
            ],
        ], $this->getSingleShopConstraint());
    }

    /**
     * @dataProvider getExpectedCommandsForMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandsForMultiShop(array $formData, array $expectedCommands): void
    {
        $builder = new SEOCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands(
            $this->getProductId(),
            $formData,
            $this->getSingleShopConstraint()
        );
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsForMultiShop(): iterable
    {
        $localizedMetaTitles = [
            1 => 'Titre français recherche',
            2 => 'English title seo',
        ];
        $localizedMetaDescriptions = [
            1 => 'Description française recherche',
            2 => 'English description seo',
        ];
        $localizedLinkRewrites = [
            1 => 'produit-francais',
            2 => 'english-product',
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42)
        ;
        yield 'single shop only' => [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_title' => false,
                    'meta_description' => $localizedMetaDescriptions,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_description' => '0',
                    'link_rewrite' => $localizedLinkRewrites,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'link_rewrite' => '',
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_PRODUCT_TEMPORARY,
                        'target' => [
                            'id' => 42,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getAllShopsCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42)
        ;
        yield 'multi-shop only' => [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_title' => true,
                    'meta_description' => $localizedMetaDescriptions,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_description' => 'enabled',
                    'link_rewrite' => $localizedLinkRewrites,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'link_rewrite' => 1,
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_PRODUCT_TEMPORARY,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'type' => true,
                        'target' => [
                            'id' => 42,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $singleCommand = $this
            ->getSingleShopCommand()
            ->setLocalizedMetaTitles($localizedMetaTitles)
            ->setLocalizedLinkRewrites($localizedLinkRewrites)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setLocalizedMetaDescriptions($localizedMetaDescriptions)
            ->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42)
        ;
        yield 'single shop and multi-shop' => [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_title' => false,
                    'meta_description' => $localizedMetaDescriptions,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_description' => true,
                    'link_rewrite' => $localizedLinkRewrites,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'link_rewrite' => false,
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_PRODUCT_TEMPORARY,
                        'target' => [
                            'id' => 42,
                            self::MODIFY_ALL_SHOPS_PREFIX . 'id' => true,
                        ],
                    ],
                ],
            ],
            [$singleCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateProductSeoCommand
    {
        return new UpdateProductSeoCommand(
            $this->getProductId()->getValue(),
            ShopConstraint::shop(self::SHOP_ID)
        );
    }

    private function getAllShopsCommand(): UpdateProductSeoCommand
    {
        return new UpdateProductSeoCommand(
            $this->getProductId()->getValue(),
            ShopConstraint::allShops()
        );
    }
}
