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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

class CommandBuilderTest extends TestCase
{
    private const MULTI_SHOP_PREFIX = 'multi_shop_';
    private const SHOP_ID = 1;

    /**
     * @dataProvider getSingleCommandParameters
     *
     * @param CommandBuilderConfig $config
     * @param array $data
     * @param array $expectedCommands
     */
    public function testBuildSingleCommand(
        CommandBuilderConfig $config,
        array $data,
        array $expectedCommands
    ): void {
        $builder = new CommandBuilder($config);
        $commands = $builder->buildCommands(
            $data,
            $this->getSingleShopCommand()
        );
        $this->assertEquals($expectedCommands, $commands);
    }

    public function getSingleCommandParameters(): iterable
    {
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addField('[name]', 'setName', DataField::TYPE_STRING)
            ->addField('[command][isValid]', 'setIsValid', DataField::TYPE_BOOL)
            ->addField('[_number]', 'setCount', DataField::TYPE_INT)
            ->addField('[parent][children]', 'setChildren', DataField::TYPE_ARRAY)
            ->addField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;
        $children = [
            'bob',
            'steve',
        ];
        $dateTime = new DateTimeImmutable('2022-10-10 15:34:45');

        $command = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setIsValid(true)
            ->setCount(42)
            ->setChildren($children)
            ->setDate($dateTime)
        ;

        yield [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'toto',
                'command' => [
                    'isValid' => true,
                ],
                '_number' => 42,
                'parent' => [
                    'children' => $children,
                ],
                'date_time' => '2022-10-10 15:34:45',
            ],
            [$command],
        ];

        // prefix is not mandatory especially when dealing with single shop command
        $config = new CommandBuilderConfig();
        $config
            ->addField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addField('[name]', 'setName', DataField::TYPE_STRING)
            ->addField('[command][isValid]', 'setIsValid', DataField::TYPE_BOOL)
            ->addField('[_number]', 'setCount', DataField::TYPE_INT)
            ->addField('[parent][children]', 'setChildren', DataField::TYPE_ARRAY)
            ->addField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;

        yield [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'toto',
                'command' => [
                    'isValid' => true,
                ],
                '_number' => 42,
                'parent' => [
                    'children' => $children,
                ],
                'date_time' => '2022-10-10 15:34:45',
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setName('toto')
            ->setIsValid(false)
        ;

        yield [
            $config,
            [
                'name' => 'toto',
                'command' => [
                    'isValid' => false,
                ],
                'unknown' => 45,
            ],
            [$command],
        ];

        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addMultiShopField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addField('[name]', 'setName', DataField::TYPE_STRING)
            ->addMultiShopField('[command][isValid]', 'setIsValid', DataField::TYPE_BOOL)
            ->addField('[_number]', 'setCount', DataField::TYPE_INT)
            ->addField('[parent][children]', 'setChildren', DataField::TYPE_ARRAY)
            ->addMultiShopField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;
        $command = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setIsValid(true)
            ->setCount(42)
            ->setDate($dateTime)
        ;

        // Same test but now some fields are multishop, since no multishop command is provided it shouldn't change the final result
        yield [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'toto',
                'command' => [
                    'isValid' => true,
                ],
                '_number' => 42,
                'unknown' => 45,
                'date_time' => '2022-10-10 15:34:45',
            ],
            [$command],
        ];

        // Handle empty date time
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;
        $command = $this
            ->getSingleShopCommand()
            ->setDate(new NullDateTime())
        ;

        // Test empty datetime
        yield [
            $config,
            [
                'date_time' => '',
            ],
            [$command],
        ];

        // Test empty datetime
        yield [
            $config,
            [
                'date_time' => null,
            ],
            [$command],
        ];

        // Single shop compound field
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addCompoundField('setOption', [
                '[option][name]' => DataField::TYPE_STRING,
                '[option][value]' => [
                    'type' => DataField::TYPE_STRING,
                    'default' => 'default',
                ],
            ])
        ;

        $command = $this
            ->getSingleShopCommand()
            ->setOption('foo', 'bar')
        ;
        yield 'single shop compound field without default value' => [
            $config,
            [
                'option' => [
                    'name' => 'foo',
                    'value' => 'bar',
                ],
            ],
            [$command],
        ];

        $command = $this
            ->getSingleShopCommand()
            ->setOption('foo', 'default')
        ;
        yield 'single shop compound field with default value' => [
            $config,
            [
                'option' => [
                    'name' => 'foo',
                ],
            ],
            [$command],
        ];
    }

    /**
     * @dataProvider getMultiShopCommandsParameters
     *
     * @param CommandBuilderConfig $config
     * @param array $data
     * @param array $expectedCommands
     */
    public function testBuildMultiShopCommands(
        CommandBuilderConfig $config,
        array $data,
        array $expectedCommands
    ): void {
        $builder = new CommandBuilder($config);
        $commands = $builder->buildCommands(
            $data,
            $this->getSingleShopCommand(),
            $this->getAllShopsCommand()
        );
        $this->assertEquals($expectedCommands, $commands);
    }

    public function getMultiShopCommandsParameters(): iterable
    {
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addMultiShopField('[name]', 'setName', DataField::TYPE_STRING)
            ->addMultiShopField('[command][isValid]', 'setIsValid', DataField::TYPE_BOOL)
            ->addMultiShopField('[_number]', 'setCount', DataField::TYPE_INT)
            ->addMultiShopField('[parent][children]', 'setChildren', DataField::TYPE_ARRAY)
            ->addMultiShopField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;
        $children = [
            'bob',
            'steve',
        ];
        $dateTime = new DateTimeImmutable('2022-10-10 15:34:45');

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setIsValid(true)
            ->setCount(42)
            ->setChildren($children)
            ->setDate($dateTime)
        ;
        yield [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'toto',
                'command' => [
                    'isValid' => true,
                ],
                '_number' => 42,
                'parent' => [
                    'children' => $children,
                ],
                'date_time' => '2022-10-10 15:34:45',
            ],
            [$singleShopCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setIsValid(false)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setCount(42)
            ->setChildren($children)
            ->setDate($dateTime)
        ;
        yield [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'toto',
                'command' => [
                    'isValid' => false,
                ],
                '_number' => 42,
                self::MULTI_SHOP_PREFIX . '_number' => true,
                'parent' => [
                    'children' => $children,
                    self::MULTI_SHOP_PREFIX . 'children' => true,
                ],
                'date_time' => '2022-10-10 15:34:45',
                self::MULTI_SHOP_PREFIX . 'date_time' => true,
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
        yield [
            $config,
            [
                '_number' => 42,
                self::MULTI_SHOP_PREFIX . '_number' => true,
                'parent' => [
                    'children' => $children,
                    self::MULTI_SHOP_PREFIX . 'children' => true,
                ],
                'date_time' => '2022-10-10 15:34:45',
                self::MULTI_SHOP_PREFIX . 'date_time' => true,
            ],
            [$allShopsCommand],
        ];

        // More advanced use, multishop field is present but not always true, and url is not a multishop field
        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setCount(42)
            ->setIsValid(false)
            ->setDate($dateTime)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setChildren($children)
        ;
        yield [
            $config,
            [
                'url' => 'http://localhost',
                self::MULTI_SHOP_PREFIX . 'url' => true,
                'name' => 'toto',
                'command' => [
                    'isValid' => false,
                ],
                '_number' => 42,
                self::MULTI_SHOP_PREFIX . '_number' => false,
                'parent' => [
                    'children' => $children,
                    self::MULTI_SHOP_PREFIX . 'children' => true,
                ],
                'date_time' => '2022-10-10 15:34:45',
                self::MULTI_SHOP_PREFIX . 'date_time' => false,
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        // Same test but now url is a multishop field
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addMultiShopField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addMultiShopField('[name]', 'setName', DataField::TYPE_STRING)
            ->addMultiShopField('[command][isValid]', 'setIsValid', DataField::TYPE_BOOL)
            ->addMultiShopField('[_number]', 'setCount', DataField::TYPE_INT)
            ->addMultiShopField('[parent][children]', 'setChildren', DataField::TYPE_ARRAY)
            ->addField('[date_time]', 'setDate', DataField::TYPE_DATETIME)
        ;

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setName('toto')
            ->setCount(42)
            ->setIsValid(false)
            ->setDate($dateTime)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setUrl('http://localhost')
            ->setChildren($children)
        ;
        yield [
            $config,
            [
                'url' => 'http://localhost',
                self::MULTI_SHOP_PREFIX . 'url' => true,
                'name' => 'toto',
                'command' => [
                    'isValid' => false,
                ],
                '_number' => 42,
                self::MULTI_SHOP_PREFIX . '_number' => false,
                'parent' => [
                    'children' => $children,
                    self::MULTI_SHOP_PREFIX . 'children' => true,
                ],
                'date_time' => '2022-10-10 15:34:45',
                self::MULTI_SHOP_PREFIX . 'date_time' => true,
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        // Mlti-shop compound field
        $config = new CommandBuilderConfig(self::MULTI_SHOP_PREFIX);
        $config
            ->addField('[url]', 'setUrl', DataField::TYPE_STRING)
            ->addMultiShopField('[name]', 'setName', DataField::TYPE_STRING)
            ->addMultiShopCompoundField('setOption', [
                '[option][name]' => DataField::TYPE_STRING,
                '[option][value]' => [
                    'type' => DataField::TYPE_STRING,
                    'default' => 'default',
                ],
            ])
        ;

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setOption('foo', 'bar')
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setName('whatever')
        ;
        yield 'disabled multi-shop compound field' => [
            $config,
            [
                'url' => 'http://localhost',
                'name' => 'whatever',
                self::MULTI_SHOP_PREFIX . 'name' => true,
                'option' => [
                    'name' => 'foo',
                    'value' => 'bar',
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
            ->setOption('foo', 'default')
        ;
        yield 'disabled multi-shop compound field with default value' => [
            $config,
            [
                'url' => 'http://localhost',
                self::MULTI_SHOP_PREFIX . 'url' => false,
                'name' => 'whatever',
                self::MULTI_SHOP_PREFIX . 'name' => true,
                'option' => [
                    'name' => 'foo',
                    self::MULTI_SHOP_PREFIX . 'name' => false,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setUrl('http://localhost')
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setName('whatever')
            ->setOption('foo', 'bar')
        ;
        yield 'partially enabled multi-shop compound field' => [
            $config,
            [
                'url' => 'http://localhost',
                self::MULTI_SHOP_PREFIX . 'url' => false,
                'name' => 'whatever',
                self::MULTI_SHOP_PREFIX . 'name' => true,
                'option' => [
                    'name' => 'foo',
                    self::MULTI_SHOP_PREFIX . 'name' => false,
                    'value' => 'bar',
                    self::MULTI_SHOP_PREFIX . 'value' => true,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setName('whatever')
            ->setOption('foo', 'default')
        ;
        yield 'enabled multi-shop compound field with default value' => [
            $config,
            [
                'url' => 'http://localhost',
                self::MULTI_SHOP_PREFIX . 'url' => false,
                'name' => 'whatever',
                self::MULTI_SHOP_PREFIX . 'name' => true,
                'option' => [
                    'name' => 'foo',
                    self::MULTI_SHOP_PREFIX . 'name' => true,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): CommandBuilderTestCommand
    {
        return new CommandBuilderTestCommand(
            ShopConstraint::shop(self::SHOP_ID)
        );
    }

    private function getAllShopsCommand(): CommandBuilderTestCommand
    {
        return new CommandBuilderTestCommand(
            ShopConstraint::allShops()
        );
    }
}
