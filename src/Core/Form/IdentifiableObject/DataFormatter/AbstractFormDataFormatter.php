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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractFormDataFormatter
{
    /**
     * @var string
     */
    protected $modifyAllNamePrefix;

    protected function __construct(
        string $modifyAllNamePrefix
    ) {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    protected function formatByPath(array $formData, array $pathAssociations): array
    {
        // @todo: a hook system should be integrated in this formatter abstract class for extendability
        $formattedData = [];

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->disableMagicCall()
            ->getPropertyAccessor()
        ;
        foreach ($pathAssociations as $bulkFormPath => $editFormPath) {
            try {
                $bulkValue = $propertyAccessor->getValue($formData, $bulkFormPath);
                $propertyAccessor->setValue($formattedData, $editFormPath, $bulkValue);
            } catch (AccessException $e) {
                // When the bulk data is not found it means the field was disabled, which is the expected behaviour
                // as the bulk request is a partial request not every data is expected And when it's not present
                // it means there is no modification to do so this field is simply ignored
            }
        }

        return $formattedData;
    }

    protected function formatMultiShopAssociation(string $originalField, string $formattedField): array
    {
        return [
            $originalField => $formattedField,
            $this->prefixWithModifyAllShops($originalField) => $this->prefixWithModifyAllShops($formattedField),
        ];
    }

    /**
     * Appends modify_all_shops prefix to last part of field name.
     * e.g. "[stock][delta_quantity][delta]" becomes "[stock][delta_quantity][modify_all_shops_delta]"
     *
     * @param string $field
     *
     * @return string
     */
    private function prefixWithModifyAllShops(string $field): string
    {
        preg_match_all('/\\[(.*?)\\]/', $field, $matches);

        if (empty($matches[1])) {
            return $field;
        }

        $allShopsFieldName = '';
        $lastIndex = count($matches[1]) - 1;
        foreach ($matches[1] as $index => $subFieldName) {
            if ($index === $lastIndex) {
                $allShopsFieldName .= sprintf('[%s%s]', $this->modifyAllNamePrefix, $subFieldName);
                continue;
            }

            $allShopsFieldName .= sprintf('[%s]', $subFieldName);
        }

        return $allShopsFieldName;
    }
}
