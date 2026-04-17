<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute\QueryHandler;

use ImageManager;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryHandler\GetAttributeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\EditableAttribute;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Handles query which gets attribute group for editing
 */
#[AsQueryHandler]
final class GetAttributeForEditingHandler implements GetAttributeForEditingHandlerInterface
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    public function __construct(
        AttributeRepository $attributeRepository,
        ImageTagSourceParserInterface $imageTagSourceParser
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->imageTagSourceParser = $imageTagSourceParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetAttributeForEditing $query): EditableAttribute
    {
        $attributeId = $query->getAttributeId();
        $attribute = $this->attributeRepository->get($attributeId);

        return new EditableAttribute(
            $attributeId->getValue(),
            (int) $attribute->id_attribute_group,
            $attribute->name,
            $attribute->color,
            $attribute->getAssociatedShops(),
            $this->getTextureImage($attributeId),
        );
    }

    /**
     * @param AttributeId $attributeId
     *
     * @return array|null
     */
    private function getTextureImage(AttributeId $attributeId)
    {
        $imageType = 'jpg';
        $image = _PS_IMG_DIR_ . 'co/' . $attributeId->getValue() . '.' . $imageType;

        if (!file_exists($image)) {
            return null;
        }

        $imageTag = ImageManager::thumbnail(
            $image,
            'attribute_texture_' . $attributeId->getValue() . '_thumb.' . $imageType,
            150,
            $imageType,
            true,
            true
        );

        if (empty($imageTag)) {
            return null;
        }

        $imageSize = filesize($image) / 1000;

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }
}
