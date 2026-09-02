<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\EditableAttribute;
use Symfony\Component\Routing\Router;

class AttributeFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private readonly Router $router,
        private ShopContext $shopContext,
    ) {
    }

    public function getData($id)
    {
        /** @var EditableAttribute $editableAttribute */
        $editableAttribute = $this->queryBus->handle(new GetAttributeForEditing((int) $id));
        $thumbnailTexture = [];
        $textureImage = $editableAttribute->getTextureImage();
        if ($textureImage) {
            $thumbnailTexture[] = [
                'size' => $textureImage['size'],
                'image_path' => $textureImage['path'],
                'delete_path' => $this->router->generate(
                    'admin_attributes_delete_texture_image',
                    [
                        'attributeGroupId' => $editableAttribute->getAttributeGroupId(),
                        'attributeId' => $id,
                    ]
                ),
            ];
        }

        return [
            'attribute_group' => $editableAttribute->getAttributeGroupId(),
            'name' => $editableAttribute->getLocalizedNames(),
            'color' => $editableAttribute->getColor(),
            'shop_association' => $editableAttribute->getAssociatedShopIds(),
            'texture' => $thumbnailTexture,
        ];
    }

    public function getDefaultData()
    {
        return [
            'shop_association' => $this->shopContext->getAssociatedShopIds(),
        ];
    }
}
