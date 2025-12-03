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

namespace PrestaShopBundle\ApiPlatform;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class NormalizationMapper
{
    public const NORMALIZATION_MAPPING = 'normalization_mapping';

    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        // Invalid (or absent) indexes or properties in array/objects are invalid, therefore ignored when checking isReadable
        // which is important for the normalization mapping process
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
    }

    /**
     * Modify the normalized data based on a mapping, basically it copies some values from a path to another, the original
     * path is not modified.
     *
     * @param mixed|null $normalizedData
     * @param array $context
     */
    public function mapNormalizedData(mixed &$normalizedData, array &$context): void
    {
        if (!is_object($normalizedData) && !is_array($normalizedData)) {
            return;
        }

        if (!empty($context[self::NORMALIZATION_MAPPING])) {
            $normalizationMapping = $context[self::NORMALIZATION_MAPPING];
        } elseif (!empty($context['operation']) && !empty($context['operation']->getExtraProperties()['CQRSCommandMapping'])) {
            $normalizationMapping = $context['operation']->getExtraProperties()['CQRSCommandMapping'];
        } else {
            return;
        }

        // Update mapping when it contains indexes placeholders
        $this->updateMappingIndexes($normalizedData, $normalizationMapping);

        foreach ($normalizationMapping as $originPath => $targetPath) {
            if ($this->propertyAccessor->isReadable($normalizedData, $originPath) && $this->propertyAccessor->isWritable($normalizedData, $targetPath)) {
                $this->propertyAccessor->setValue($normalizedData, $targetPath, $this->propertyAccessor->getValue($normalizedData, $originPath));
            }
        }

        // Mapping is only done once, so we unset it for next recursive calls
        unset($context[self::NORMALIZATION_MAPPING]);
    }

    /**
     * Update normalization mapping when it contains indexes, for example:
     *
     *     '[categoriesInformation][categoriesInformation][@index][id]' => '[categories][@index][categoryId]',
     *
     * is transformed into:
     *
     *     '[categoriesInformation][categoriesInformation][0][id]' => '[categories][0][categoryId]',
     *     '[categoriesInformation][categoriesInformation][1][id]' => '[categories][1][categoryId]',
     *
     * depending on the computed array length from the normalized data.
     */
    protected function updateMappingIndexes(mixed $normalizedData, array &$normalizationMapping): void
    {
        // Copy mapping that will  be modified by reference, to leave the loop one untouched while we do modifications
        $updatedNormalizationMapping = $normalizationMapping;
        foreach ($updatedNormalizationMapping as $originPath => $targetPath) {
            $this->transformMappingPath($normalizedData, $originPath, $targetPath, $normalizationMapping);
        }
    }

    /**
     * Transform one of the mapping path if it contains indexes, the original path containing placeholders is cleaned.
     */
    protected function transformMappingPath(mixed $normalizedData, string $originPath, string $targetPath, array &$normalizationMapping): void
    {
        $propertyPath = new PropertyPath($originPath);
        $indexes = [];
        for ($i = 0; $i < $propertyPath->getLength(); ++$i) {
            $property = $propertyPath->getElement($i);
            if (in_array($property, $indexes)) {
                throw new InvalidArgumentException(sprintf('You cannot use the same index twice %s', $property));
            }

            if (str_starts_with($property, '@')) {
                $indexes[] = $property;
            }
        }

        // No indexes detected nothing to do
        if (empty($indexes)) {
            return;
        }

        foreach ($indexes as $index) {
            $this->computeMappingIndex($propertyPath, $index, $normalizedData, $originPath, $targetPath, $normalizationMapping);
        }

        // Remove the generic one with index placeholders, and then the job is done
        unset($normalizationMapping[$originPath]);
    }

    /**
     * Dive through the data following a property path, when the path segment matching $indexPlaceholder is reached we
     * compute the length of the current array level and generate a mapping path for each element.
     */
    protected function computeMappingIndex(PropertyPath $propertyPath, string $indexPlaceholder, mixed $normalizedData, string $originPath, string $targetPath, array &$normalizationMapping): void
    {
        $data = $normalizedData;
        for ($elementIndex = 0; $elementIndex < $propertyPath->getLength(); ++$elementIndex) {
            $property = $propertyPath->getElement($elementIndex);
            // We reached the path segment containing the index placeholder
            if ($property === $indexPlaceholder) {
                // We can only transform indexes for arrays
                if (!is_array($data)) {
                    return;
                }

                // Build new mapping with hard-coded indexes
                for ($index = 0; $index < count($data); ++$index) {
                    $originIndexPath = str_replace($indexPlaceholder, (string) $index, $originPath);
                    $targetIndexPath = str_replace($indexPlaceholder, (string) $index, $targetPath);
                    $normalizationMapping[$originIndexPath] = $targetIndexPath;
                }

                return;
            } else {
                // We keep diving into the data
                $subPath = $propertyPath->isIndex($elementIndex) ? '[' . $property . ']' : '.' . $property;
                if (!$this->propertyAccessor->isReadable($data, $subPath)) {
                    return;
                }
                $data = $this->propertyAccessor->getValue($data, $subPath);
            }
        }
    }
}
