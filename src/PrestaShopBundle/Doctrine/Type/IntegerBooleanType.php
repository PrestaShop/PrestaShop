<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Type;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BooleanType;

/**
 * Boolean columns are 0/1 integers on every engine (TINYINT(1) on MySQL, SMALLINT on PostgreSQL,
 * so legacy code reading them keeps getting 0/1): the value is bound as an integer instead of
 * the native boolean PostgreSQL would refuse to store in a SMALLINT.
 */
final class IntegerBooleanType extends BooleanType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        return null === $value ? null : (int) (bool) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?bool
    {
        return null === $value ? null : (bool) $value;
    }

    public function getBindingType(): int
    {
        return ParameterType::INTEGER;
    }
}
