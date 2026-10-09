<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\SqlManagement\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

class SqlRequestCommandTest extends TestCase
{
    public function testEditAcceptsBothAScalarIdAndAValueObject(): void
    {
        self::assertSame(1, (new EditSqlRequestCommand(1))->getSqlRequestId()->getValue());
        self::assertSame(1, (new EditSqlRequestCommand(new SqlRequestId(1)))->getSqlRequestId()->getValue());
    }

    public function testDeleteAcceptsBothAScalarIdAndAValueObject(): void
    {
        self::assertSame(1, (new DeleteSqlRequestCommand(1))->getSqlRequestId()->getValue());
        self::assertSame(1, (new DeleteSqlRequestCommand(new SqlRequestId(1)))->getSqlRequestId()->getValue());
    }
}
