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

namespace PrestaShop\PrestaShop\Core\Repository;

use ObjectModel;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\ExceptionBuilder;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShopException;

/**
 * This abstract class provides the base helper function to build a repository service based on the use of legacy
 * objects from PrestaShop. You need to implement the required methods (get, add, update, ...) into your repository,
 * but you can use this abstract class to do most of the job.
 */
abstract class AbstractObjectModelRepository
{
    /**
     * @param int $id
     * @param string $objectTableName
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function assertObjectModelExists(int $id, string $objectTableName, string $exceptionClass, int $errorCode = 0): void
    {
        try {
            if (!ObjectModel::existsInDatabase($id, $objectTableName)) {
                throw new $exceptionClass(sprintf('%s #%d does not exist', $objectTableName, $id), $errorCode);
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to check if %s #%d exists [%s]',
                    $objectTableName,
                    $id,
                    $e->getMessage()
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param int $id
     * @param string $objectModelClass
     * @param string $exceptionClass
     *
     * @return ObjectModel
     *
     * @throws CoreException
     */
    protected function getObjectModel(int $id, string $objectModelClass, string $exceptionClass): ObjectModel
    {
        return $this->fetchObjectModel($id, $objectModelClass, $exceptionClass, null);
    }

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
                    sprintf('Failed to add %s', $objectModel::class),
                    $errorCode
                );
            }

            return (int) $objectModel->id;
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to add %s [%s]',
                    $objectModel::class,
                    $e->getMessage()
                ),
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
    protected function updateObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0): void
    {
        if (!$objectModel->id) {
            throw new CoreException('Cannot update object model without id');
        }

        try {
            if (!$objectModel->update()) {
                throw new $exceptionClass(
                    sprintf('Failed to update %s #%d', $objectModel::class, $objectModel->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to update %s #%d [%s]',
                    $objectModel::class,
                    $objectModel->id,
                    $e->getMessage()
                ),
                0,
                $e
            );
        } finally {
            $objectModel->setFieldsToUpdate(null);
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param array $propertiesToUpdate
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function partiallyUpdateObjectModel(
        ObjectModel $objectModel,
        array $propertiesToUpdate,
        string $exceptionClass,
        int $errorCode = 0
    ): void {
        $objectModel->setFieldsToUpdate($this->formatPropertiesToUpdate($propertiesToUpdate));
        $this->updateObjectModel($objectModel, $exceptionClass, $errorCode);
    }

    /**
     * @param ObjectModel $objectModel
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function deleteObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0): void
    {
        try {
            if (!$objectModel->delete()) {
                throw new $exceptionClass(
                    sprintf('Failed to delete %s #%d', $objectModel::class, $objectModel->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to delete %s #%d [%s]',
                    $objectModel::class,
                    $objectModel->id,
                    $e->getMessage()
                ),
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
    protected function softDeleteObjectModel(ObjectModel $objectModel, string $exceptionClass, int $errorCode = 0): void
    {
        try {
            if (!$objectModel->softDelete()) {
                throw new $exceptionClass(
                    sprintf('Failed to soft delete %s #%d', $objectModel::class, $objectModel->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to soft delete %s #%d [%s]',
                    $objectModel::class,
                    $objectModel->id,
                    $e->getMessage()
                ),
                0,
                $e
            );
        }
    }

    /**
     * Expected format: $propertiesToUpdate = [
     *     'active', // Regular field are simply listed
     *     'price',
     *     'name' => [ // Multilang fields must indicate which language is impacted
     *         1,
     *         3,
     *     ],
     * ];
     *
     * @param array $propertiesToUpdate
     *
     * @return array<string, mixed>
     */
    protected function formatPropertiesToUpdate(array $propertiesToUpdate): array
    {
        $formattedPropertiesToUpdate = [];
        foreach ($propertiesToUpdate as $propertyName => $property) {
            if (!is_array($property) && !is_string($property)) {
                throw new InvalidArgumentException('Invalid format for properties to update, expected an array indexed by string matching field name');
            }

            // For common properties the value is the field name
            if (!is_array($property)) {
                $formattedPropertiesToUpdate[$property] = true;

                continue;
            }

            // For multilang values the index is actually the field name
            foreach ($property as $langId) {
                $formattedPropertiesToUpdate[$propertyName][$langId] = true;
            }
        }

        return $formattedPropertiesToUpdate;
    }

    /**
     * @param int $id
     * @param string $objectModelClass
     * @param string $exceptionClass
     * @param int|null $shopId
     *
     * @return ObjectModel
     *
     * @throws CoreException
     */
    protected function fetchObjectModel(int $id, string $objectModelClass, string $exceptionClass, ?int $shopId): ObjectModel
    {
        try {
            $objectModel = $this->constructObjectModel($id, $objectModelClass, $shopId);
            if ((int) $objectModel->id !== $id) {
                throw ExceptionBuilder::buildException($exceptionClass, sprintf('%s #%d was not found', $objectModelClass, $id), 0, null, $id);
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to get %s #%d [%s]',
                    $objectModelClass,
                    $id,
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $objectModel;
    }

    /**
     * This method can be overridden in case your ObjectModel has a special constructor
     *
     * @param int $id
     * @param string $objectModelClass
     * @param int|null $shopId
     *
     * @return ObjectModel
     */
    protected function constructObjectModel(int $id, string $objectModelClass, ?int $shopId): ObjectModel
    {
        return new $objectModelClass($id, null, $shopId);
    }
}
