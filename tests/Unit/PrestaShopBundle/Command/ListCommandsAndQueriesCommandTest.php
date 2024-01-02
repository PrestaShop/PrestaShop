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

namespace Tests\Unit\PrestaShopBundle\Command;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\Resource\ResourceNameCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinitionParser;
use PrestaShopBundle\Command\ListCommandsAndQueriesCommand;
use PrestaShopBundle\Exception\DomainClassNameMalformedException;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandsAndQueriesCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /**
     * @var ResourceNameCollectionFactoryInterface|MockObject
     */
    private $resourceNameCollectionMock;

    /**
     * @var ResourceMetadataCollectionFactoryInterface|MockObject
     */
    private $resourceMetadataCollectionMock;

    public function setUp(): void
    {
        $this->resourceNameCollectionMock = $this->createMock(ResourceNameCollectionFactoryInterface::class);
        $this->resourceMetadataCollectionMock = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);

        $command = new ListCommandsAndQueriesCommand(
            new CommandDefinitionParser(),
            $this->getListOfCQRSCommands(),
            $this->resourceNameCollectionMock,
            $this->resourceMetadataCollectionMock
        );

        $this->commandTester = new CommandTester($command);

        parent::setUp();
    }

    /**
     * @dataProvider optionsProvider
     */
    public function testExecute(array $options, string $result): void
    {
        $this->resourceNameCollectionMock->method('create')->willReturn(new ResourceNameCollection());

        $this->resourceMetadataCollectionMock->method('create')->willReturn(new ResourceMetadataCollection(''));

        $this->commandTester->execute($options);

        static::assertEquals($result,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithWrongClass(): void
    {
        $this->resourceNameCollectionMock->method('create')->willReturn(new ResourceNameCollection());
        $this->resourceMetadataCollectionMock->method('create')->willReturn(new ResourceMetadataCollection(''));

        $command = new ListCommandsAndQueriesCommand(
            new CommandDefinitionParser(),
            ["PrestaShop\PrestaShop\Adapter\HookManager"],
            $this->resourceNameCollectionMock,
            $this->resourceMetadataCollectionMock
        );

        $commandTester = new CommandTester($command);

        $this->expectException(DomainClassNameMalformedException::class);
        $commandTester->execute([]);
    }

    public function optionsProvider(): array
    {
        return [
            [
                [],
                "1.\nClass: PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand\nType: Command\nAPI: \n\n\n2.\nClass: PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand\nType: Command\nAPI: \n\n\n3.\nClass: PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand\nType: Command\nAPI: \nThis command adds/sends message to the customer related with the order.\n\n4.\nClass: PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing\nType: Query\nAPI: \nGets customer thread for viewing\n\n",
            ],
            [
                [
                    '--domain' => ['CustomerMessage'],
                ],
                "3.\nClass: PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand\nType: Command\nAPI: \nThis command adds/sends message to the customer related with the order.\n\n",
            ],
            [
                [
                    '--format' => 'simple',
                ],
                "PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand NOT OK\nPrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand NOT OK\nPrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand NOT OK\nPrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing NOT OK\n",
            ],
            [
                [
                    '--domain' => ['CustomerMessage'],
                    '--format' => 'simple',
                ],
                "PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand NOT OK\n",
            ],
        ];
    }

    /**
     * @return string[]
     */
    private function getListOfCQRSCommands(): array
    {
        return [
            "PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand",
            "PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand",
            "PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand",
            "PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing",
        ];
    }
}
