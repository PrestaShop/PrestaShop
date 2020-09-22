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
 * Reusable methods to persist legacy object model
 */
abstract class AbstractObjectModelPersister
{
    /**
     * @param ObjectModel $objectModel
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @return int
     */
    protected function addObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0): int
    {
        try {
            if (!$objectModel->add()) {
                throw new $exceptionClass(
                    sprintf('Failed to add %s #%d', get_class($objectModel), $objectModel->id),
                    $errorCode
                );
            }

            return (int) $objectModel->id;
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to add %s #%d', get_class($objectModel), $objectModel->id),
                0,
                $e
            );
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function updateObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0)
    {
        try {
            if (!$objectModel->update()) {
                throw new $exceptionClass(
                    sprintf('Failed to update %s #%d', get_class($objectModel), $objectModel->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to update %s #%d', get_class($objectModel), $objectModel->id),
                0,
                $e
            );
        } finally {
            $objectModel->setFieldsToUpdate(null);
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param bool $soft
     *
     * @return bool
     *
     * @throws CoreException
     */
    protected function deleteObjectModel(ObjectModel $objectModel, bool $soft = false): bool
    {
        try {
            return (bool) ($soft ? $objectModel->softDelete() : $objectModel->delete());
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to delete %s #%d', get_class($objectModel), $objectModel->id),
                0,
                $e
            );
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param array $properties
     */
    protected function fillProperty(ObjectModel $objectModel, string $propertyName, array $properties)
    {
        if (!array_key_exists($propertyName, $properties)) {
            return;
        }

        $objectModel->{$propertyName} = $properties[$propertyName];

        if ($objectModel->id) {
            $objectModel->addFieldsToUpdate([$propertyName => true]);
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param array $properties
     */
    protected function fillLocalizedProperty(ObjectModel $objectModel, string $propertyName, array $properties)
    {
        if (!array_key_exists($propertyName, $properties)) {
            return;
        }

        if (!is_array($properties[$propertyName])) {
            throw new CoreException(sprintf(
                'Localized object model property must be an array. "%s" given',
                var_export($properties[$propertyName])
            ));
        }

        $objectModel->{$propertyName} = $properties[$propertyName];

        if ($objectModel->id) {
            $this->addLocalizedPropertyToUpdate($objectModel, $propertyName, $properties[$propertyName]);
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param array<int, string> $values
     */
    private function addLocalizedPropertyToUpdate(ObjectModel $objectModel, string $propertyName, array $values): void
    {
        $updateFieldValue = [];
        foreach ($values as $langId => $value) {
            $updateFieldValue[$langId] = true;
        }

        $objectModel->addFieldsToUpdate([$propertyName => $updateFieldValue]);
    }
}
