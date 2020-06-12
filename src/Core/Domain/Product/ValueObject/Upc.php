<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product upc code value
 */
class Upc
{
    /**
     * Valid upc regex pattern
     */
    const VALID_PATTERN = '/^[0-9]{0,12}$/';

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->assertUpcIsValid($value);
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    private function assertUpcIsValid(string $value): void
    {
        if (preg_match(self::VALID_PATTERN, $value)) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid UPC "%s". It should match pattern "%s"',
                $value,
                self::VALID_PATTERN
            ),
            ProductConstraintException::INVALID_UPC
        );
    }
}
