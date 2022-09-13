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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierUpdate;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\CombinationSupplierAssociation;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Associates supplier with product combination
 *
 * @see UpdateCombinationSuppliersHandlerInterface
 */
class UpdateCombinationSuppliersCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var array<int, ProductSupplierUpdate>
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
     * @return array<int, ProductSupplierUpdate>
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
            throw new InvalidArgumentException(sprintf(
                'Empty array of combination suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
        }

        foreach ($productSuppliers as $productSupplier) {
            $this->combinationSuppliers[] = new ProductSupplierUpdate(
                new CombinationSupplierAssociation(
                    $this->combinationId->getValue(),
                    $productSupplier['supplier_id'],
                    !empty($productSupplier['product_supplier_id']) ? $productSupplier['product_supplier_id'] : null
                ),
                $productSupplier['currency_id'],
                $productSupplier['reference'],
                $productSupplier['price_tax_excluded']
            );
        }
    }
}
