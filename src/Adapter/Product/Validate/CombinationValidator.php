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

namespace PrestaShop\PrestaShop\Adapter\Product\Validate;

use Combination;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class CombinationValidator extends AbstractObjectModelValidator
{
    /**
     * @param Combination $combination
     */
    public function validate(Combination $combination): void
    {
        $this->validatePrices($combination);
    }

    /**
     * @param Combination $combination
     *
     * @throws CoreException
     * @throws ProductConstraintException
     */
    private function validatePrices(Combination $combination)
    {
        $this->validateCombinationProperty($combination, 'price', CombinationConstraintException::INVALID_PRICE);
        $this->validateCombinationProperty($combination, 'ecotax', CombinationConstraintException::INVALID_ECOTAX);
        $this->validateCombinationProperty($combination, 'unit_price_impact', CombinationConstraintException::INVALID_UNIT_PRICE);
    }

    /**
     * @param Combination $combination
     * @param string $property
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws ProductConstraintException
     */
    private function validateCombinationProperty(Combination $combination, string $property, int $errorCode): void
    {
        $this->validateObjectModelProperty($combination, $property, CombinationConstraintException::class, $errorCode);
    }
}
