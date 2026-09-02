<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject;

/**
 * Manufacturer Identifier is a ValueObject which represents a valid identifier of a manufacturer.
 * It is being used in every class that must refer to a Manufacturer object or is linked to a Manufacturer object.
 *
 * However it is possible to decide to un-link the class from a Manufacturer. For example a product can be linked to a Manufacturer now and later this relationship is removed.
 *
 * This class NoManufacturerId carriers this intent, instead of using `null` which has another meaning (no modification).
 *
 * This picture might help understanding the situation: https://pbs.twimg.com/media/DusCOfyXcAA9_F7.jpg
 */
class NoManufacturerId implements ManufacturerIdInterface
{
    public const NO_MANUFACTURER_ID = 0;

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return static::NO_MANUFACTURER_ID;
    }
}
