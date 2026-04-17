<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler\GetSupplierForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Handles query which gets supplier for editing
 */
#[AsQueryHandler]
final class GetSupplierForEditingHandler extends AbstractSupplierHandler implements GetSupplierForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    public function __construct(ImageTagSourceParserInterface $imageTagSourceParser)
    {
        $this->imageTagSourceParser = $imageTagSourceParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSupplierForEditing $query)
    {
        $supplierId = $query->getSupplierId();
        $supplier = $this->getSupplier($supplierId);
        $address = $this->getSupplierAddress($supplierId);

        return new EditableSupplier(
            $supplierId,
            $supplier->name,
            $supplier->description,
            $address->address1,
            $address->city,
            $address->address2,
            (int) $address->id_country,
            $address->postcode,
            (int) $address->id_state,
            $address->phone,
            $address->phone_mobile,
            $supplier->meta_title,
            $supplier->meta_description,
            (bool) $supplier->active,
            $supplier->getAssociatedShops(),
            $address->dni,
            $this->getLogoImage($supplierId->getValue())
        );
    }

    /**
     * @param int $imageId
     *
     * @return array|null
     */
    private function getLogoImage(int $imageId): ?array
    {
        $imagePath = _PS_SUPP_IMG_DIR_ . $imageId . '.jpg';
        $imageTag = $this->getTmpImageTag($imagePath, $imageId, 'supplier');
        $imageSize = $this->getImageSize($imagePath);

        if (empty($imageTag) || null === $imageSize) {
            return null;
        }

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }
}
