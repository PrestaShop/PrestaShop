<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\QueryHandler;

use ImageManager;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler\GetManufacturerForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Handles query which gets manufacturer for editing
 */
#[AsQueryHandler]
final class GetManufacturerForEditingHandler extends AbstractManufacturerHandler implements GetManufacturerForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetManufacturerForEditing $query)
    {
        $manufacturerId = $query->getManufacturerId();
        $manufacturer = $this->getManufacturer($manufacturerId);

        return new EditableManufacturer(
            $manufacturerId,
            $manufacturer->name,
            (bool) $manufacturer->active,
            $manufacturer->short_description,
            $manufacturer->description,
            $manufacturer->meta_title,
            $manufacturer->meta_description,
            $this->getLogoImage($manufacturerId),
            $manufacturer->getAssociatedShops()
        );
    }

    /**
     * @param ManufacturerId $manufacturerId
     *
     * @return array|null
     */
    private function getLogoImage(ManufacturerId $manufacturerId)
    {
        $pathToImage = _PS_MANU_IMG_DIR_ . $manufacturerId->getValue() . '.jpg';
        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'manufacturer_' . $manufacturerId->getValue() . '.jpg',
            350,
            'jpg',
            true,
            true
        );

        $imageSize = file_exists($pathToImage) ? filesize($pathToImage) / 1000 : '';

        if (empty($imageTag) || empty($imageSize)) {
            return null;
        }

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }
}
