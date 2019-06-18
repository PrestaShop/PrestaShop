<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\AbstractAttributeGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\CommandHandler\DeleteAttributeGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\DeleteAttributeGroupException;

/**
 * Handles command which deletes attribute group using legacy object model
 */
final class DeleteAttributeGroupHandler extends AbstractAttributeGroupHandler implements DeleteAttributeGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttributeGroupException
     */
    public function handle(DeleteAttributeGroupCommand $command)
    {
        $attributeGroupId = $command->getAttributeGroupId();
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        if (false === $this->deleteAttributeGroup($attributeGroup)) {
            throw new DeleteAttributeGroupException(
                sprintf('Failed deleting attribute group with id "%s"', $attributeGroupId->getValue()),
                DeleteAttributeGroupException::FAILED_DELETE
            );
        }
    }
}
