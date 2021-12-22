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

namespace PrestaShop\PrestaShop\Adapter\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Supplier\AbstractSupplierHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler\GetSupplierForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Handles query which gets supplier for editing
 */
final class GetSupplierForEditingHandler extends AbstractSupplierHandler implements GetSupplierForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @var int
     */
    private $contextShopId;

    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        $contextShopId
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->contextShopId = $contextShopId;
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
            $supplier->meta_keywords,
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
