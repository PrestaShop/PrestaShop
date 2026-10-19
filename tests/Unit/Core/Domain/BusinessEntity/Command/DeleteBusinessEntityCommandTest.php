<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\DeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;

class DeleteBusinessEntityCommandTest extends TestCase
{
    public function testItExposesBusinessEntityIdViaGetter(): void
    {
        $command = new DeleteBusinessEntityCommand(7);

        $this->assertSame(7, $command->getBusinessEntityId()->getValue());
    }

    public function testItRejectsNonPositiveId(): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new DeleteBusinessEntityCommand(0);
    }
}
