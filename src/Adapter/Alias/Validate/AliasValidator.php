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
