<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Type;

use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * PostgreSQL stores the MySQL zero date legacy code writes as -infinity, which an entity reads as null.
 */
final class ZeroDateAwareDateType extends DateType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeInterface
    {
        if (DateTimeUtil::POSTGRESQL_NULL_DATE === $value) {
            return null;
        }

        return parent::convertToPHPValue($value, $platform);
    }
}
