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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\SetCombinationSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier;
use RuntimeException;

/**
 * Associates supplier with product combination
 *
 * @see SetCombinationSuppliersHandlerInterface
 */
class SetCombinationSuppliersCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var array<int, ProductSupplier>
     */
    private $combinationSuppliers;

    /**
     * @param int $combinationId
     * @param array<int, array<string, string|int|null>> $combinationSuppliers
     */
    public function __construct(
        int $combinationId,
        array $combinationSuppliers
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->setCombinationSuppliers($combinationSuppliers);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return array<int, ProductSupplier>
     */
    public function getCombinationSuppliers(): array
    {
        return $this->combinationSuppliers;
    }

    /**
     * @param array<int, array<string, string|int|null>> $productSuppliers
     */
    private function setCombinationSuppliers(array $productSuppliers): void
    {
        if (empty($productSuppliers)) {
            throw new RuntimeException(sprintf(
                'Empty array of combination suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedCombinationSuppliersCommand::class
            ));
        }

        foreach ($productSuppliers as $productSupplier) {
            $this->combinationSuppliers[] = new ProductSupplier(
                $productSupplier['supplier_id'],
                $productSupplier['currency_id'],
                $productSupplier['reference'],
                $productSupplier['price_tax_excluded'],
                $productSupplier['product_supplier_id'] ?? null
            );
        }
    }
}
