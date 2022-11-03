<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
