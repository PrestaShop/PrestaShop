<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\Validate;

use Alias;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates alias field using legacy object model
 */
class AliasValidator extends AbstractObjectModelValidator
{
    /**
     * This method is specific for alias creation only.
     *
     * @param Alias $alias
     *
     * @throws CoreException
     */
    public function validate(Alias $alias): void
    {
        $this->validateAliasProperty($alias, 'search', AliasConstraintException::INVALID_SEARCH);
        $this->validateAliasProperty($alias, 'alias', AliasConstraintException::INVALID_ALIAS);
        $this->validateAliasProperty($alias, 'active', AliasConstraintException::INVALID_VISIBILITY);
    }

    /**
     * @param Alias $alias
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws AliasConstraintException
     */
    private function validateAliasProperty(Alias $alias, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $alias,
            $propertyName,
            AliasConstraintException::class,
            $errorCode
        );
    }
}
