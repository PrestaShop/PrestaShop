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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\SEOCommandsBuilder;

class SEOCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new SEOCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        yield [
            [
                'no_price_data' => ['useless value'],
            ],
            [],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        yield [
            [
                'seo' => [
                    'not_handled' => 0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $localizedMetaTitles = [
            1 => 'Titre français recherche',
            2 => 'English title seo',
        ];
        $command->setLocalizedMetaTitles($localizedMetaTitles);
        yield [
            [
                'seo' => [
                    'meta_title' => $localizedMetaTitles,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $localizedMetaDescriptions = [
            1 => 'Description française recherche',
            2 => 'English description seo',
        ];
        $command->setLocalizedMetaDescriptions($localizedMetaDescriptions);
        yield [
            [
                'seo' => [
                    'meta_description' => $localizedMetaDescriptions,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $localizedLinkRewrites = [
            1 => 'produit-francais',
            2 => 'english-product',
        ];
        $command->setLocalizedLinkRewrites($localizedLinkRewrites);
        yield [
            [
                'seo' => [
                    'link_rewrite' => $localizedLinkRewrites,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $command->setLocalizedLinkRewrites($localizedLinkRewrites);
        $command->setLocalizedMetaTitles($localizedMetaTitles);
        $command->setLocalizedMetaDescriptions($localizedMetaDescriptions);
        yield [
            [
                'seo' => [
                    'link_rewrite' => $localizedLinkRewrites,
                    'meta_description' => $localizedMetaDescriptions,
                    'meta_title' => $localizedMetaTitles,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $command->setRedirectOption(RedirectType::TYPE_NOT_FOUND, 0);
        yield [
            [
                'seo' => [
                    'redirect_option' => [
                        'type' => RedirectType::TYPE_NOT_FOUND,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $command->setRedirectOption(RedirectType::TYPE_PRODUCT_TEMPORARY, 42);
        yield [
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

        $command = new UpdateProductSeoCommand($this->getProductId()->getValue());
        $command->setRedirectOption(RedirectType::TYPE_CATEGORY_TEMPORARY, 51);
        yield [
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
    }
}
