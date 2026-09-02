<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
