<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\TaxRulesGroup\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\EditTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;

class EditTaxRulesGroupCommandTest extends TestCase
{
    public function testShopAssociation(): void
    {
        $command = new EditTaxRulesGroupCommand(0);

        $this->assertEquals(null, $command->getShopAssociation());
        $this->assertInstanceOf(EditTaxRulesGroupCommand::class, $command->setShopAssociation([1, 3, 5]));
        $this->assertEquals([1, 3, 5], $command->getShopAssociation());
    }

    public function testShopAssociationNotInteger(): void
    {
        $command = new EditTaxRulesGroupCommand(0);

        $this->expectException(TaxRulesGroupConstraintException::class);
        $this->expectExceptionCode(TaxRulesGroupConstraintException::INVALID_SHOP_ASSOCIATION);
        $this->expectExceptionMessage('Given shop association array (
  0 => 1,
  1 => \'3\',
  2 => 5,
) must contain only integer values');

        /* @phpstan-ignore-next-line */
        $command->setShopAssociation([1, '3', 5]);
    }
}
