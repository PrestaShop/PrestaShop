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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use TaxRulesGroup;

/**
 * Validates TaxRulesGroup properties using legacy object model validation
 */
class TaxRulesGroupValidator extends AbstractObjectModelValidator
{
    /**
     * @param TaxRulesGroup $taxRulesGroup
     */
    public function validate(TaxRulesGroup $taxRulesGroup): void
    {
        $this->validateTaxRulesGroupProperty(
            $taxRulesGroup,
            'name',
            TaxRulesGroupConstraintException::INVALID_NAME
        );
        $this->validateTaxRulesGroupProperty(
            $taxRulesGroup,
            'active',
            TaxRulesGroupConstraintException::INVALID_ACTIVE
        );
        $this->validateTaxRulesGroupProperty(
            $taxRulesGroup,
            'deleted',
            TaxRulesGroupConstraintException::INVALID_DELETED
        );
        $this->validateTaxRulesGroupProperty(
            $taxRulesGroup,
            'date_add',
            TaxRulesGroupConstraintException::INVALID_CREATION_DATE
        );
        $this->validateTaxRulesGroupProperty(
            $taxRulesGroup,
            'date_upd',
            TaxRulesGroupConstraintException::INVALID_UPDATE_DATE
        );
    }

    /**
     * @param TaxRulesGroup $taxRulesGroup
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws TaxRulesGroupConstraintException
     */
    private function validateTaxRulesGroupProperty(
        TaxRulesGroup $taxRulesGroup,
        string $propertyName,
        int $errorCode = 0
    ): void {
        $this->validateObjectModelProperty(
            $taxRulesGroup,
            $propertyName,
            TaxRulesGroupConstraintException::class,
            $errorCode
        );
    }
}
