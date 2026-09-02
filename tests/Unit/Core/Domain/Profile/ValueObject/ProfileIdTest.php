<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Profile\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

class ProfileIdTest extends TestCase
{
    /**
     * @dataProvider createsProfileWithValidValuesData
     */
    public function testItCreatesProfileWithValidValues($idValue)
    {
        $profileId = new ProfileId($idValue);

        $this->assertEquals((int) $idValue, $profileId->getValue());
    }

    public function createsProfileWithValidValuesData()
    {
        return [
            [1],
            [42],
        ];
    }

    /**
     * @dataProvider exceptionThrownWithInvalidValuesData
     */
    public function testItExceptionThrownWithInvalidValues($profileId)
    {
        $this->expectException(ProfileException::class);
        new ProfileId($profileId);
    }

    public function exceptionThrownWithInvalidValuesData()
    {
        return [
            [-1],
            [0],
        ];
    }
}
