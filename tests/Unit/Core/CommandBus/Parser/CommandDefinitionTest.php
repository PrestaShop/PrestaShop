<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\CommandBus\Parser;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinition;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;

class CommandDefinitionTest extends TestCase
{
    public function testItCanBeBuilt()
    {
        $commandDefinition = new CommandDefinition(
            EditTaxCommand::class,
            'Command',
            'Edits given tax with provided data'
        );

        $this->assertInstanceOf(CommandDefinition::class, $commandDefinition);
    }
}
