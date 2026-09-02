<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
