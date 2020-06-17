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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinitionParser;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use ReflectionException;

class CommandDefinitionParserTest extends TestCase
{
    public function testItProvidesCorrectClassNameWhenExistingCommandNameIsGiven()
    {
        $commandDefinitionParser = new CommandDefinitionParser();

        $expected = EditTaxCommand::class;

        $actual = $commandDefinitionParser->parseDefinition(EditTaxCommand::class)->getClassName();

        $this->assertEquals($expected, $actual);
    }

    public function testItProvidesCorrectCommandTypeWhenCommandOfTypeQueryIsGiven()
    {
        $commandDefinitionProvider = new CommandDefinitionParser();

        $expected = 'Query';

        $actual = $commandDefinitionProvider->parseDefinition(GetTaxForEditing::class)->getCommandType();

        $this->assertEquals($expected, $actual);
    }

    public function testItProvidesCorrectCommandTypeWhenCommandofTypeCommandIsGiven()
    {
        $commandDefinitionProvider = new CommandDefinitionParser();

        $expected = 'Command';

        $actual = $commandDefinitionProvider->parseDefinition(EditTaxCommand::class)->getCommandType();

        $this->assertEquals($expected, $actual);
    }

    public function testItProvidesCorrectDescriptionWhenCommandWithoutAnnotationsInDocBlockIsGiven()
    {
        $commandDefinitionProvider = new CommandDefinitionParser();

        $expected = 'Edits given tax with provided data';

        $actual = $commandDefinitionProvider->parseDefinition(EditTaxCommand::class)->getDescription();

        $this->assertEquals($expected, $actual);
    }

    public function testItThrowsExceptionWhenNonExistingCommandNameIsGiven()
    {
        $this->expectException(ReflectionException::class);

        $commandDefinitionProvider = new CommandDefinitionParser();
        $commandDefinitionProvider->parseDefinition('FailCommandThatDoesntExist');
    }
}
