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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

use PrestaShop\PrestaShop\Core\Util\String\ModifyAllShopsUtil;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractFormDataFormatter
{
    /**
     * @var string
     */
    protected $modifyAllNamePrefix;

    public function __construct(
        string $modifyAllNamePrefix = ''
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

            try {
                $modifyAllShopsPath = ModifyAllShopsUtil::prefixFieldPathWithAllShops($bulkFormPath, $this->modifyAllNamePrefix);
                $modifyAllShopsValue = $propertyAccessor->getValue($formData, $modifyAllShopsPath);
                $propertyAccessor->setValue(
                    $formattedData,
                    ModifyAllShopsUtil::prefixFieldPathWithAllShops($editFormPath, $this->modifyAllNamePrefix),
                    $modifyAllShopsValue
                );
            } catch (AccessException $e) {
                // this means the field does not have related modify_all_shops field, so it is not multiShop field
                // therefore we don't need to re-format its related modify_all_shops field name
            }
        }

        return $formattedData;
    }
}
