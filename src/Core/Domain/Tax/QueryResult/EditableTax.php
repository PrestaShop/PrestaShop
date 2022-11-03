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

namespace PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Transfers editable tax data
 */
class EditableTax
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var bool
     */
    private $active;

    /**
     * EditableTax constructor.
     *
     * @param TaxId $taxId
     * @param string[] $localizedNames
     * @param float $rate
     * @param bool $active
     */
    public function __construct(TaxId $taxId, array $localizedNames, $rate, $active)
    {
        $this->taxId = $taxId;
        $this->localizedNames = $localizedNames;
        $this->rate = $rate;
        $this->active = $active;
    }

    /**
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }
}
