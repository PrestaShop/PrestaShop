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
        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $commands = $builder->buildCommands($data, $command);
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
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
        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $allShopsCommand = new CommandBuilderTestCommand(ShopConstraint::allShops());
        $commands = $builder->buildCommands($data, $command, $allShopsCommand);
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setIsValid(false)
        ;

        $allShopsCommand = new CommandBuilderTestCommand(ShopConstraint::allShops());
        $allShopsCommand
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
            [$command, $allShopsCommand],
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
        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
            ->setUrl('http://localhost')
            ->setName('toto')
            ->setCount(42)
            ->setIsValid(false)
            ->setDate($dateTime)
        ;

        $allShopsCommand = new CommandBuilderTestCommand(ShopConstraint::allShops());
        $allShopsCommand
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
            [$command, $allShopsCommand],
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

        $command = new CommandBuilderTestCommand(ShopConstraint::shop(self::SHOP_ID));
        $command
            ->setName('toto')
            ->setCount(42)
            ->setIsValid(false)
            ->setDate($dateTime)
        ;

        $allShopsCommand = new CommandBuilderTestCommand(ShopConstraint::allShops());
        $allShopsCommand
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
            [$command, $allShopsCommand],
        ];
    }
}
