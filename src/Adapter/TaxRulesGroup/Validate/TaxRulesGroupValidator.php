<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
