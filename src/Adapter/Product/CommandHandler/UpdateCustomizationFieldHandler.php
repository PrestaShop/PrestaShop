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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractCustomizationFieldHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Exception\CustomizationFieldException;
use PrestaShopException;

/**
 * Updates single customization field using legacy object model
 */
class UpdateCustomizationFieldHandler extends AbstractCustomizationFieldHandler implements UpdateCustomizationFieldHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCustomizationFieldCommand $command): void
    {
        $fieldIdValue = $command->getCustomizationFieldId()->getValue();
        $fieldEntity = $this->getCustomizationField($fieldIdValue);

        $fieldEntity->type = $command->getCustomizationFieldId()->getValue();
        $fieldEntity->is_module = $command->isAddedByModule();
        $fieldEntity->required = $command->isRequired();
        $fieldEntity->name = $command->getLocalizedNames();

        if (!$fieldEntity->validateFields(false) || !$fieldEntity->validateFieldsLang(false)) {
            throw new CannotUpdateCustomizationFieldException('Customization field contains invalid values');
        }

        try {
            if (false === $fieldEntity->update()) {
                throw new CannotUpdateCustomizationFieldException(sprintf(
                    'Failed to update customization field #%s',
                    $fieldEntity->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CustomizationFieldException(sprintf(
                'Error occurred when trying to update customization field #%d',
                $fieldEntity->id
            ));
        }
    }
}
