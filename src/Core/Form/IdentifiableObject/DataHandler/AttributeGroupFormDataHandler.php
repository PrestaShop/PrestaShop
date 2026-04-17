<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\AddAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\EditAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Handles data of submitted Attribute Group form.
 */
final class AttributeGroupFormDataHandler implements FormDataHandlerInterface
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
        /** @var AttributeGroupId $attributeGroupId */
        $attributeGroupId = $this->commandBus->handle(new AddAttributeGroupCommand(
            $data['name'],
            $data['public_name'],
            $data['group_type'],
            $data['shop_association']
        ));

        return $attributeGroupId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $command = new EditAttributeGroupCommand((int) $id);
        $command
            ->setLocalizedNames($data['name'])
            ->setLocalizedPublicNames($data['public_name'])
            ->setType($data['group_type'])
            ->setAssociatedShopIds($data['shop_association'])
        ;
        $this->commandBus->handle($command);
    }
}
