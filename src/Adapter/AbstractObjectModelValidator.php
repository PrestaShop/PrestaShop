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

namespace PrestaShop\PrestaShop\Adapter;

use ObjectModel;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * Reusable methods for validating legacy object models
 */
abstract class AbstractObjectModelValidator
{
    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function validateObjectModelProperty(ObjectModel $objectModel, string $propertyName, string $exceptionClass, int $errorCode = 0): void
    {
        try {
            if (true !== $objectModel->validateField($propertyName, $objectModel->{$propertyName})) {
                throw new $exceptionClass(
                    sprintf(
                        'Invalid %s %s. Got "%s"',
                        get_class($objectModel),
                        $propertyName,
                        $objectModel->{$propertyName}
                    ),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when validating %s property "%s"', get_class($objectModel), $propertyName),
                0,
                $e
            );
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function validateObjectModelLocalizedProperty(ObjectModel $objectModel, string $propertyName, string $exceptionClass, int $errorCode = 0)
    {
        $localizedValues = $objectModel->{$propertyName};

        try {
            foreach ($localizedValues as $langId => $value) {
                if (true !== $objectModel->validateField($propertyName, $value, $langId)) {
                    throw new $exceptionClass(
                        sprintf(
                            'Invalid %s localized property "%s" for language with id "%d"',
                            get_class($objectModel),
                            $propertyName,
                            $langId
                        ),
                        $errorCode
                    );
                }
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to validate %s localized property "%s"', get_class($objectModel), $propertyName),
                0,
                $e
            );
        }
    }
}
