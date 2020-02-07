<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\AbstractAttributeHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Command\BulkDeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\CommandHandler\BulkDeleteAttributeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\DeleteAttributeException;

/**
 * Handles command which deletes attributes in bulk action using legacy object model
 */
final class BulkDeleteAttributeHandler extends AbstractAttributeHandler implements BulkDeleteAttributeHandlerInterface
{
    /**
     * @param BulkDeleteAttributeCommand $command
     *
     * @throws AttributeException
     */
    public function handle(BulkDeleteAttributeCommand $command)
    {
        foreach ($command->getAttributeIds() as $attributeId) {
            $attribute = $this->getAttributeById($attributeId);

            if (false === $this->deleteAttribute($attribute)) {
                throw new DeleteAttributeException(sprintf('Failed to delete attribute with id "%s"', $attribute->id), DeleteAttributeException::FAILED_BULK_DELETE);
            }
        }
    }
}
