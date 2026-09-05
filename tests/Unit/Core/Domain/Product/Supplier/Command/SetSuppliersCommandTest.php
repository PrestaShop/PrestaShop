<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Supplier\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;

final class SetSuppliersCommandTest extends TestCase
{
    public function testNumericStringSupplierIdsAreConvertedToIntegers(): void
    {
        $command = new SetSuppliersCommand(15, ['1', '2']);

        $supplierIds = $command->getSupplierIds();

        self::assertCount(2, $supplierIds);
        self::assertSame(1, $supplierIds[0]->getValue());
        self::assertSame(2, $supplierIds[1]->getValue());
    }
}
