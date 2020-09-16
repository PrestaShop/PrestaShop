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
use RuntimeException;

/**
 * Reusable methods to add/update legacy object model
 */
abstract class AbstractObjectModelUpdater
{
    /**
     * @var <array<string, bool|array<int, bool>>
     */
    protected $propertiesToUpdate = [];

    /**
     * @param ObjectModel $objectModel
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function updateObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0)
    {
        $objectModel->setFieldsToUpdate($this->propertiesToUpdate);
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
            $this->propertiesToUpdate = [];
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param array $propertiesToUpdate
     */
    protected function fillProperty(ObjectModel $objectModel, string $propertyName, array $propertiesToUpdate)
    {
        if (!array_key_exists($propertyName, $propertiesToUpdate)) {
            return;
        }

        $objectModel->{$propertyName} = $propertiesToUpdate[$propertyName];
        $this->propertiesToUpdate[$propertyName] = true;
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $propertyName
     * @param array $propertiesToUpdate
     */
    protected function fillLocalizedProperty(ObjectModel $objectModel, string $propertyName, array $propertiesToUpdate)
    {
        if (!array_key_exists($propertyName, $propertiesToUpdate)) {
            return;
        }

        if (!is_array($propertiesToUpdate[$propertyName])) {
            throw new RuntimeException(sprintf(
                'Localized object model property must be an array. "%s" given',
                var_export($propertiesToUpdate[$propertyName])
            ));
        }

        $objectModel->{$propertyName} = $propertiesToUpdate[$propertyName];
        $this->addLocalizedPropertyToUpdate($propertyName, $propertiesToUpdate[$propertyName]);
    }

    /**
     * @param string $propertyName
     * @param array<int, string> $values
     */
    private function addLocalizedPropertyToUpdate(string $propertyName, array $values): void
    {
        foreach ($values as $langId => $value) {
            $this->propertiesToUpdate[$propertyName][$langId] = true;
        }
    }
}
