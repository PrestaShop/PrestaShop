<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;


use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * This type of product code is specific to Europe and Japan, but is widely used internationally.
 * It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.
 */
class Ean13
{
    /**
     * @var int
     */
    private $reference;

    /**
     * @param int $reference
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $reference)
    {
        $this->assertIsValidNumber($reference);

        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->reference;
    }

    /**
     * @param int $reference
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidNumber(int $reference): void
    {
        $pattern = '/^\d{0,13}$/';
        if (!preg_match($pattern, $reference)) {
            throw new ProductConstraintException(
                sprintf(
                    'Product Ean13 reference "%s" did not matched pattern "%s"',
                    $reference,
                    $pattern
                ),
                ProductConstraintException::INVALID_EAN13
            );
        }
    }
}
