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

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProductsDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryHandler\GetPackedProductsDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;

/**
 * Handles GetPackedProductsDetails query using legacy object model
 */
final class GetPackedProductsDetailsHandler implements GetPackedProductsDetailsHandlerInterface
{
    /**
     * @var ProductPackRepository
     */
    private $productRepository;

    /**
     * @var CombinationNameBuilder
     */
    private $combinationNameBuilder;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @param int $defaultLangId
     * @param ProductPackRepository $productPackRepository
     * @param AttributeRepository $attributeRepository
     * @param CombinationNameBuilder $combinationNameBuilder
     * @param ProductImageRepository $productImageRepository
     */
    public function __construct(
        int $defaultLangId,
        ProductPackRepository $productPackRepository,
        AttributeRepository $attributeRepository,
        CombinationNameBuilder $combinationNameBuilder,
        ProductImageRepository $productImageRepository
    ) {
        $this->productRepository = $productPackRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->languageId = $defaultLangId;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetPackedProductsDetails $query): array
    {
        $packedItems = $this->productRepository->getPackedProducts(
            $query->getPackId(),
            $query->getLanguageId()
        );
        $packedProducts = [];
        foreach ($packedItems as $packedItem) {
            $combinationId = (int) $packedItem['id_product_attribute_item'];
            $preview = $this->productImageRepository->getPreview(
                new ProductId((int) $packedItem['id_product_item']),
                $query->getLanguageId(),
                $combinationId === 0 ? null : new CombinationId($combinationId)
            );
            $name = $packedItem['name'];
            if ($combinationId > 0) {
                $name .= ' - ' . $this->getCombinationName(new CombinationId($combinationId));
            }

            $packedProducts[] = new PackedProductDetails(
                (int) $packedItem['id_product_item'],
                (int) $packedItem['quantity'],
                (int) $combinationId,
                (string) $name,
                (string) $packedItem['reference'],
                $preview->getImage()
            );
        }

        return $packedProducts;
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return string
     */
    private function getCombinationName(CombinationId $combinationId): string
    {
        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            [$combinationId],
            new LanguageId($this->languageId)
        );

        return $this->combinationNameBuilder->buildName($attributesInformation[$combinationId->getValue()]);
    }
}
