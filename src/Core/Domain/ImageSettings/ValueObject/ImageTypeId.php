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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;

/**
 * Defines Image Type ID with it's constraints
 */
class ImageTypeId
{
    private int $imageTypeId;

    /**
     * @param int $imageTypeId
     *
     * @throws ImageTypeException
     */
    public function __construct(int $imageTypeId)
    {
        $this->assertIntegerIsGreaterThanZero($imageTypeId);
        $this->imageTypeId = $imageTypeId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->imageTypeId;
    }

    /**
     * @param int $imageTypeId
     *
     * @throws ImageTypeException
     */
    private function assertIntegerIsGreaterThanZero(int $imageTypeId): void
    {
        if (0 >= $imageTypeId) {
            throw new ImageTypeException(sprintf('Image type id %d is invalid. Image type id have to be number bigger than zero.', $imageTypeId));
        }
    }
}
