<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Security\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\EmployeeSessionId;

class EmployeeSessionIdTest extends TestCase
{
    /**
     * @dataProvider createsSessionIdWithValidValuesData
     */
    public function testItCreatesSessionIdWithValidValues($idValue)
    {
        $sessionId = new EmployeeSessionId($idValue);

        $this->assertEquals((int) $idValue, $sessionId->getValue());
    }

    public function createsSessionIdWithValidValuesData()
    {
        return [
            [1],
            [42],
        ];
    }

    /**
     * @dataProvider exceptionThrownWithInvalidValuesData
     */
    public function testItExceptionThrownWithInvalidValues($sessionId)
    {
        $this->expectException(SessionException::class);
        new EmployeeSessionId($sessionId);
    }

    public function exceptionThrownWithInvalidValuesData()
    {
        return [
            [0],
            [-1],
        ];
    }
}
