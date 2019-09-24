<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Update tax rules group with provided data
 */
class UpdateTaxRulesGroupCommand
{
    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int[]|null
     */
    private $shopAssociation;

    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @param int $taxRulesGroupId
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(int $taxRulesGroupId)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return UpdateTaxRulesGroupCommand
     */
    public function setName(?string $name): UpdateTaxRulesGroupCommand
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[]|null $shopAssociation
     *
     * @return UpdateTaxRulesGroupCommand
     */
    public function setShopAssociation(?array $shopAssociation): UpdateTaxRulesGroupCommand
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return UpdateTaxRulesGroupCommand
     */
    public function setEnabled(bool $enabled): UpdateTaxRulesGroupCommand
    {
        $this->enabled = $enabled;

        return $this;
    }
}
