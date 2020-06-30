<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

use CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldNotFoundException;
use PrestaShopException;

abstract class AbstractCustomizationFieldHandler extends AbstractProductHandler
{
    /**
     * @param int $fieldId
     *
     * @return CustomizationField
     *
     * @throws CustomizationFieldException
     * @throws CustomizationFieldNotFoundException
     */
    protected function getCustomizationField(int $fieldId): CustomizationField
    {
        try {
            $field = new CustomizationField($fieldId);

            if ((int) $field->id !== $fieldId) {
                throw new CustomizationFieldNotFoundException(sprintf(
                    'Customization field #%d was not found',
                    $fieldId
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CustomizationFieldException(
                sprintf('Error occurred when trying to get customization field #%d', $fieldId),
                0,
                $e
            );
        }

        return $field;
    }
}
