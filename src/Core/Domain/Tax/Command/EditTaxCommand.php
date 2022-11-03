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

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Edits given tax with provided data
 */
class EditTaxCommand
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @var array|null
     */
    private $localizedNames;

    /**
     * @var float|null
     */
    private $rate;

    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @param int $taxId
     *
     * @throws TaxException
     */
    public function __construct($taxId)
    {
        $this->taxId = new TaxId($taxId);
    }

    /**
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @return array|null
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param array|null $localizedNames
     *
     * @return self
     */
    public function setLocalizedNames($localizedNames)
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float|null $rate
     *
     * @return self
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
