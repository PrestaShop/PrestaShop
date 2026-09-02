<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Configuration;
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
                        $objectModel::class,
                        $propertyName,
                        $objectModel->{$propertyName}
                    ),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when validating %s property "%s"', $objectModel::class, $propertyName),
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
        $localizedValues = $objectModel->{$propertyName} ?? [];

        try {
            $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
            if (!isset($localizedValues[$defaultLang])) {
                // The value for the default must always be set, so we put an empty string if it does not exist
                $localizedValues[$defaultLang] = '';
            }

            foreach ($localizedValues as $langId => $value) {
                if (true !== $objectModel->validateField($propertyName, $value, $langId)) {
                    throw new $exceptionClass(
                        sprintf(
                            'Invalid %s localized property "%s" for language with id "%d"',
                            $objectModel::class,
                            $propertyName,
                            $langId
                        ),
                        $errorCode
                    );
                }
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to validate %s localized property "%s"', $objectModel::class, $propertyName),
                0,
                $e
            );
        }
    }
}
