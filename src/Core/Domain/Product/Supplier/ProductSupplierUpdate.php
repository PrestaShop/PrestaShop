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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier;

use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\SupplierAssociationInterface;

/**
 * This class is a DTO containing the elements for a supplier's update, it is used in commands
 * related to updating product suppliers.
 */
class ProductSupplierUpdate
{
    /**
     * @var SupplierAssociationInterface
     */
    private $association;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $priceTaxExcluded;

    /**
     * @param SupplierAssociationInterface $association
     * @param int $currencyId
     * @param string $reference
     * @param string $priceTaxExcluded
     */
    public function __construct(
        SupplierAssociationInterface $association,
        int $currencyId,
        string $reference,
        string $priceTaxExcluded
    ) {
        $this->association = $association;
        $this->currencyId = new CurrencyId($currencyId);
        $this->reference = $reference;
        $this->priceTaxExcluded = $priceTaxExcluded;
    }

    /**
     * @return SupplierAssociationInterface
     */
    public function getAssociation(): SupplierAssociationInterface
    {
        return $this->association;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getPriceTaxExcluded(): string
    {
        return $this->priceTaxExcluded;
    }
}
