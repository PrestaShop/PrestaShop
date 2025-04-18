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

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

        foreach ($normalizationMapping as $originPath => $targetPath) {
            if ($this->propertyAccessor->isReadable($normalizedData, $originPath) && $this->propertyAccessor->isWritable($normalizedData, $targetPath)) {
                $this->propertyAccessor->setValue($normalizedData, $targetPath, $this->propertyAccessor->getValue($normalizedData, $originPath));
            }
        }

        // Mapping is only done once, so we unset it for next recursive calls
        unset($context[self::NORMALIZATION_MAPPING]);
    }
}
