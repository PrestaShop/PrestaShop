<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

class SetCarrierTaxRuleGroupCommand
{
    private CarrierId $carrierId;

    private TaxRulesGroupId $carrierTaxRuleGroupId;

    public function __construct(
        int $carrierId,
        int $carrierTaxRuleGroupId,
        private ShopConstraint $shopConstraint
    ) {
        $this->assertShopConstraint($this->shopConstraint);
        $this->carrierId = new CarrierId($carrierId);
        $this->carrierTaxRuleGroupId = new TaxRulesGroupId($carrierTaxRuleGroupId);
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getCarrierTaxRuleGroupId(): TaxRulesGroupId
    {
        return $this->carrierTaxRuleGroupId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    private function assertShopConstraint(ShopConstraint $shopConstraint): void
    {
        if (!$shopConstraint->forAllShops()) {
            throw new CarrierConstraintException(
                'Shop constraint isn\'t supported yet.',
                CarrierConstraintException::INVALID_SHOP_CONSTRAINT
            );
        }
    }
}
