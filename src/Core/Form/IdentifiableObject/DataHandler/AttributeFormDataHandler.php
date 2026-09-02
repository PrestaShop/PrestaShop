<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $addAttributeCommand = new AddAttributeCommand(
            $data['attribute_group'],
            $data['name'],
            $data['color'] ?? '',
            $data['shop_association'],
        );

        if (isset($data['texture'])) {
            /** @var UploadedFile $file */
            $file = $data['texture'];

            $addAttributeCommand->setTextureFilePath(
                $file->getPathname()
            );
        }

        /** @var AttributeId $attributeId */
        $attributeId = $this->commandBus->handle($addAttributeCommand);

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

        if (isset($data['texture'])) {
            /** @var UploadedFile $file */
            $file = $data['texture'];

            $updateCommand->setTextureFilePath(
                $file->getPathname()
            );
        }

        $this->commandBus->handle($updateCommand);
    }
}
