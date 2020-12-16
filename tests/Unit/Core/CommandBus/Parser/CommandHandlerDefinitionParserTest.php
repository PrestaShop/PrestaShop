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

namespace Tests\Unit\Core\CommandBus\Parser;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Address\CommandHandler\AddManufacturerAddressHandler;
use PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler\EditManufacturerHandler;
use PrestaShop\PrestaShop\Adapter\Manufacturer\QueryHandler\GetManufacturerForEditingHandler;
use PrestaShop\PrestaShop\Adapter\Meta\CommandHandler\EditMetaHandler;
use PrestaShop\PrestaShop\Adapter\Product\CommandHandler\UpdateProductBasicInformationHandler;
use PrestaShop\PrestaShop\Adapter\Product\QueryHandler\GetProductForEditingHandler;
use PrestaShop\PrestaShop\Adapter\Profile\CommandHandler\EditProfileHandler;
use PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler\AddSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Tax\CommandHandler\AddTaxHandler;
use PrestaShop\PrestaShop\Adapter\Tax\CommandHandler\EditTaxHandler;
use PrestaShop\PrestaShop\Adapter\Tax\QueryHandler\GetTaxForEditingHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinitionParser;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\UpdateCustomerThreadStatusHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\UpdateCustomerThreadStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler\GetManufacturerForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\EditProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\EditTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use ReflectionException;

class CommandHandlerDefinitionParserTest extends TestCase
{
    /**
     * @var CommandHandlerDefinitionParser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new CommandHandlerDefinitionParser();

        parent::setUp();
    }

    /**
     * @dataProvider getDataForInterfacesAssertion
     *
     * @param string $handler
     * @param string $command
     * @param array $expectedInterfaces
     */
    public function testItProvidesCorrectInterfacesWhenExistingHandlerIsGiven(string $handler, string $command, array $expectedInterfaces): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);
        $this->assertEquals($expectedInterfaces, $definition->getHandlerInterfaces());
    }

    /**
     * @return Generator
     */
    public function getDataForInterfacesAssertion(): Generator
    {
        yield [EditTaxHandler::class, EditTaxCommand::class, [EditTaxHandlerInterface::class]];
        yield [GetManufacturerForEditingHandler::class, GetManufacturerForEditing::class, [GetManufacturerForEditingHandlerInterface::class]];
        yield [UpdateCustomerThreadStatusHandler::class, UpdateCustomerThreadStatusCommand::class, [UpdateCustomerThreadStatusHandlerInterface::class]];
    }

    /**
     * @dataProvider getDataForClassNamesAssertion
     *
     * @param string $handler
     * @param string $command
     */
    public function testItProvidesCorrectClassNames(string $handler, string $command): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);

        Assert::assertSame($handler, $definition->getHandlerClass());
        Assert::assertSame($command, $definition->getCommandClass());
    }

    /**
     * @return Generator
     */
    public function getDataForClassNamesAssertion(): Generator
    {
        yield [EditTaxHandler::class, EditTaxCommand::class];
        yield [EditMetaHandler::class, EditMetaCommand::class];
    }

    /**
     * @dataProvider getDataForTypeAssertion
     *
     * @param string $handler
     * @param string $command
     * @param string $expectedType
     */
    public function testItProvidesCorrectType(string $handler, string $command, string $expectedType): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);

        $this->assertEquals($expectedType, $definition->getType());
    }

    /**
     * @return Generator
     */
    public function getDataForTypeAssertion(): Generator
    {
        yield [GetTaxForEditingHandler::class, GetTaxForEditing::class, 'query'];
        yield [EditTaxHandler::class, EditTaxCommand::class, 'command'];
    }

    /**
     * @dataProvider getDataForDescriptionAssertion
     *
     * @param string $handler
     * @param string $command
     * @param string $expectedDescription
     */
    public function testItProvidesCorrectDescription(string $handler, string $command, string $expectedDescription): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);

        $this->assertEquals($expectedDescription, $definition->getDescription());
    }

    public function getDataForDescriptionAssertion(): Generator
    {
        yield [EditTaxHandler::class, EditTaxCommand::class, 'Edits given tax with provided data'];
        yield [EditManufacturerHandler::class, EditManufacturerCommand::class, 'Edits manufacturer with provided data'];
        yield [EditProfileHandler::class, EditProfileCommand::class, 'Edits existing Profile'];
    }

    /**
     * @dataProvider getDataForReflectionExceptionAssertion
     *
     * @param string $handler
     * @param string $command
     */
    public function testItThrowsExceptionWhenNonExistingCommandOrHandlerNameIsGiven(string $handler, string $command): void
    {
        $this->expectException(ReflectionException::class);

        $this->parser->parseDefinition($handler, $command);
    }

    /**
     * @return Generator
     */
    public function getDataForReflectionExceptionAssertion(): Generator
    {
        yield [EditTaxHandler::class, 'randomNoSuchClass'];
        yield ['randomNoSuchHandlerclass', AddManufacturerCommand::class];
    }

    /**
     * @dataProvider getDataForReturnTypeAssertion
     *
     * @param string $handler
     * @param string $command
     * @param string|null $returnType
     */
    public function testItProvidesCorrectReturnType(string $handler, string $command, ?string $returnType): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);
        Assert::assertSame($returnType, $definition->getReturnType());
    }

    /**
     * @return Generator
     */
    public function getDataForReturnTypeAssertion(): Generator
    {
        yield [AddTaxHandler::class, AddTaxCommand::class, null];
        yield [UpdateProductBasicInformationHandler::class, UpdateProductBasicInformationCommand::class, 'void'];
        yield [GetProductForEditingHandler::class, GetProductForEditing::class, 'ProductForEditing'];
        yield [AddManufacturerAddressHandler::class, AddManufacturerAddressCommand::class, 'AddressId'];
    }

    /**
     * @dataProvider getDataForConstructorParamsAssertion
     *
     * @param string $handler
     * @param string $command
     * @param array $expectedParams
     */
    public function testItProvidesCorrectCommandConstructorParams(string $handler, string $command, array $expectedParams): void
    {
        $definition = $this->parser->parseDefinition($handler, $command);

        Assert::assertSame($expectedParams, $definition->getCommandConstructorParams());
    }

    /**
     * @return Generator
     */
    public function getDataForConstructorParamsAssertion(): Generator
    {
        yield [AddTaxHandler::class, AddTaxCommand::class, ['array localizedNames', 'rate', 'enabled']];
        yield [GetProductForEditingHandler::class, GetProductForEditing::class, ['int productId']];
        yield [AddSupplierHandler::class, AddSupplierCommand::class, [
            'string name',
            'string address',
            'string city',
            'int countryId',
            'bool enabled',
            'array localizedDescriptions',
            'array localizedMetaTitles',
            'array localizedMetaDescriptions',
            'array localizedMetaKeywords',
            'array shopAssociation',
            '?string address2 = NULL',
            '?string postCode = NULL',
            '?int stateId = NULL',
            '?string phone = NULL',
            '?string mobilePhone = NULL',
            '?string dni = NULL',
        ]];
    }
}
