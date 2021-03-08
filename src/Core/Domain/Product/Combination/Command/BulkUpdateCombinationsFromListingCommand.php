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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\BulkUpdateCombinationsFromListingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ListedCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates a list of combinations
 *
 * @see BulkUpdateCombinationsFromListingHandlerInterface
 */
class BulkUpdateCombinationsFromListingCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ListedCombinationForEditing[]
     */
    private $listedCombinationsForEditing;

    /**
     * @param int $productId
     * @param array<int, array<string, string|bool|int|null>> $listedCombinationsData
     */
    public function __construct(
        int $productId,
        array $listedCombinationsData
    ) {
        $this->productId = new ProductId($productId);
        $this->setListedCombinationsForEditing($listedCombinationsData);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ListedCombinationForEditing[]
     */
    public function getListedCombinationsForEditing(): array
    {
        return $this->listedCombinationsForEditing;
    }

    /**
     * @param array $listedCombinationsData
     */
    private function setListedCombinationsForEditing(array $listedCombinationsData): void
    {
        foreach ($listedCombinationsData as $combinationData) {
            $listedCombinationForEditing = new ListedCombinationForEditing((int) $combinationData['combination_id']);
            if (isset($combinationData['impact_on_price'])) {
                $listedCombinationForEditing->setImpactOnPrice($combinationData['impact_on_price']);
            }
            if (isset($combinationData['quantity'])) {
                $listedCombinationForEditing->setQuantity((int) $combinationData['quantity']);
            }
            if (isset($combinationData['is_default'])) {
                $listedCombinationForEditing->setDefault((bool) $combinationData['is_default']);
            }
            $this->listedCombinationsForEditing[] = $listedCombinationForEditing;
        }
    }
}
