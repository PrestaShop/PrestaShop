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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;

/**
 * Handles data of submitted Attribute Group form.
 */
final class AttributeFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        /** @var AttributeId $attributeId */
        $attributeId = $this->commandBus->handle(new AddAttributeCommand(
            $data['attribute_group'],
            $data['name'],
            $data['color'] ?? '',
            $data['shop_association']
        ));

        return $attributeId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $updateCommand = new EditAttributeCommand($id);
        $updateCommand->setAttributeGroupId($data['attribute_group'])
            ->setLocalizedNames($data['name'])
            ->setColor($data['color'])
            ->setAssociatedShopIds($data['shop_association']);

        $this->commandBus->handle($updateCommand);
    }
}
