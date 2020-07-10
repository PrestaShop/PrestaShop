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
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\DeleteCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\DeleteCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;

/**
 * Handles @var DeleteCustomizationFieldCommand using legacy object model
 */
final class DeleteCustomizationFieldHandler extends AbstractCustomizationFieldHandler implements DeleteCustomizationFieldHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCustomizationFieldCommand $command): void
    {
        $fieldEntity = $this->getCustomizationField($command->getCustomizationFieldId());
        $fieldId = (int) $fieldEntity->id;
        $product = $this->getProduct(new ProductId($fieldId));
        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());

        try {
            if (in_array($fieldId, $usedFieldIds)) {
                $successfullyDeleted = $fieldEntity->softDelete();
            } else {
                $successfullyDeleted = $fieldEntity->delete();
            }
        } catch (PrestaShopException $e) {
            throw new CustomizationFieldException(
                sprintf(
                    'Error occurred when trying to delete customization field #%d',
                    $fieldId
                ),
                0,
                $e
            );
        }

        if (!$successfullyDeleted) {
            throw new CannotDeleteCustomizationFieldException(sprintf(
                'Failed deleting customization field #%d',
                    $fieldId
                )
            );
        }

        $this->refreshProductCustomizability($product);
        $this->refreshCustomizationFieldsCount($product);
        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS);
    }
}
