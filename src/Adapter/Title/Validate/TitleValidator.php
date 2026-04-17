<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title\Validate;

use Gender;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates TaxRulesGroup properties using legacy object model validation
 */
class TitleValidator extends AbstractObjectModelValidator
{
    /**
     * @param Gender $title
     *
     * @throws TitleConstraintException
     * @throws CoreException
     */
    public function validate(Gender $title): void
    {
        $this->validateObjectModelLocalizedProperty(
            $title,
            'name',
            TitleConstraintException::class,
            TitleConstraintException::INVALID_NAME
        );
        $this->validateTitleProperty(
            $title,
            'type',
            TitleConstraintException::INVALID_TYPE
        );
    }

    /**
     * @param Gender $title
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws TitleConstraintException
     * @throws CoreException
     */
    private function validateTitleProperty(
        Gender $title,
        string $propertyName,
        int $errorCode = 0
    ): void {
        $this->validateObjectModelProperty(
            $title,
            $propertyName,
            TitleConstraintException::class,
            $errorCode
        );
    }
}
