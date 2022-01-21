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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryHandler\GetPackedProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;

/**
 * Handles GetPackedProducts query using legacy object model
 */
class GetPackedProductsHandler implements GetPackedProductsHandlerInterface
{
    /**
     * @var ProductPackRepository
     */
    protected $productRepository;

    /**
     * @var CombinationNameBuilder
     */
    protected $combinationNameBuilder;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var int
     */
    protected $languageId;

    /**
     * @var ProductImageRepository
     */
    protected $productImageRepository;

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
    public function handle(GetPackedProducts $query): array
    {
        $packedItems = $this->productRepository->getPackedProducts(
            $query->getPackId(),
            $query->getLanguageId()
        );
        $packedProducts = [];
        $combinationIds = [];
        foreach ($packedItems as $packedItem) {
            $combinationId = (int) $packedItem['id_product_attribute_item'];

            if ($combinationId > 0) {
                $combinationIds[] = new CombinationId($combinationId);
            }
        }
        $attributesInformations = $this->attributeRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            new LanguageId($this->languageId)
        );

        foreach ($packedItems as $packedItem) {
            $combinationId = (int) $packedItem['id_product_attribute_item'];
            if ($combinationId === NoCombinationId::NO_COMBINATION_ID) {
                $coverUrl = $this->productImageRepository->getProductCoverUrl(
                    new ProductId((int) $packedItem['id_product_item'])
                );
            } else {
                $coverUrl = $this->productImageRepository->getCombinationCoverUrl(
                    new CombinationId($combinationId)
                );
            }
            $name = $packedItem['name'];
            if ($combinationId > 0) {
                $name = $this->combinationNameBuilder->buildFullName($name, $attributesInformations[$combinationId]);
            }

            $packedProducts[] = new PackedProductDetails(
                (int) $packedItem['id_product_item'],
                (int) $packedItem['quantity'],
                (int) $combinationId,
                (string) $name,
                (string) $packedItem['reference'],
                $coverUrl
            );
        }

        return $packedProducts;
    }
}
