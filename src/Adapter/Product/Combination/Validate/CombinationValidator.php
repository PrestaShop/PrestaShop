<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Validate;

use Combination;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates Combination properties using legacy object model
 */
class CombinationValidator extends AbstractObjectModelValidator
{
    /**
     * @param Combination $combination
     */
    public function validate(Combination $combination): void
    {
        $this->validateDetails($combination);
        $this->validatePrices($combination);
        $this->validateStock($combination);
    }

    /**
     * @param Combination $combination
     *
     * @throws CoreException
     * @throws ProductConstraintException
     */
    private function validateDetails(Combination $combination): void
    {
        $this->validateCombinationProperty($combination, 'ean13', ProductConstraintException::INVALID_EAN_13);
        $this->validateCombinationProperty($combination, 'isbn', ProductConstraintException::INVALID_ISBN);
        $this->validateCombinationProperty($combination, 'mpn', ProductConstraintException::INVALID_MPN);
        $this->validateCombinationProperty($combination, 'reference', ProductConstraintException::INVALID_REFERENCE);
        $this->validateCombinationProperty($combination, 'upc', ProductConstraintException::INVALID_UPC);
        $this->validateCombinationProperty($combination, 'weight', ProductConstraintException::INVALID_WEIGHT);
    }

    /**
     * @param Combination $combination
     *
     * @throws CoreException
     * @throws ProductConstraintException
     */
    private function validatePrices(Combination $combination): void
    {
        $this->validateCombinationProperty($combination, 'price', ProductConstraintException::INVALID_PRICE);
        $this->validateCombinationProperty($combination, 'ecotax', ProductConstraintException::INVALID_ECOTAX);
        $this->validateCombinationProperty($combination, 'unit_price_impact', ProductConstraintException::INVALID_UNIT_PRICE);
        $this->validateCombinationProperty($combination, 'wholesale_price', ProductConstraintException::INVALID_WHOLESALE_PRICE);
    }

    /**
     * @param Combination $combination
     *
     * @throws CoreException
     * @throws ProductConstraintException
     */
    private function validateStock(Combination $combination): void
    {
        $this->validateCombinationProperty($combination, 'minimal_quantity', ProductConstraintException::INVALID_MINIMAL_QUANTITY);
        $this->validateCombinationProperty($combination, 'low_stock_threshold', ProductConstraintException::INVALID_LOW_STOCK_THRESHOLD);
        $this->validateCombinationProperty($combination, 'low_stock_alert', ProductConstraintException::INVALID_LOW_STOCK_ALERT);
        $this->validateCombinationProperty($combination, 'available_date', ProductConstraintException::INVALID_AVAILABLE_DATE);
        $this->validateCombinationLocalizedProperty($combination, 'available_later', ProductConstraintException::INVALID_AVAILABLE_LATER);
        $this->validateCombinationLocalizedProperty($combination, 'available_now', ProductConstraintException::INVALID_AVAILABLE_NOW);
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
        $this->validateObjectModelProperty($combination, $property, ProductConstraintException::class, $errorCode);
    }

    /**
     * @param Combination $combination
     * @param string $property
     * @param int $errorCode
     *
     * @throws ProductConstraintException
     */
    private function validateCombinationLocalizedProperty(Combination $combination, string $property, int $errorCode = 0): void
    {
        $this->validateObjectModelLocalizedProperty(
            $combination,
            $property,
            ProductConstraintException::class,
            $errorCode
        );
    }
}
